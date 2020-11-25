require('leaflet');
require('leaflet-responsive-popup');
require('leaflet-freedraw');
require('leaflet-kml');
const carto = require('@carto/carto.js');
require('paginationjs');

class EstateMap {
    constructor(id) {
        this.initCache(id);
        this.initMap();
        this.initDrawButtons();
        this.initSliderCheckTimer();
        this.initEvents();
    }

    initCache(id) {
        this.$body = $('body');
        this.$window = $(window);

        this.$map = $(id);
        this.$iw = this.$map.closest('.js-map-interface-wrap');

        this.$mapContainer = this.$iw.find('.js-map-container');
        this.$drawButton = this.$iw.find('.js-map-draw');
        this.$drawCancel = this.$iw.find('.js-draw-cancel');
        this.$drawApply = this.$iw.find('.js-draw-apply');
        this.$drawEdit = this.$iw.find('.js-draw-edit');
        this.$showSchools = this.$iw.find('.js-map-show-schools');
        this.$estateCardsWrap = this.$iw.find('.js-estate-cards-wrap');
        this.$estateCardsList = this.$iw.find('.js-estate-cards-list');
        this.$cardsScrollWrap = this.$iw.find('.js-cards-scroll-wrap');
        this.$estateCardsPagination = this.$iw.find('.js-estate-cards-pagination');
        this.$yelpSelect = this.$iw.find('.js-yelp-select');

        this.dataParams = this.$map.data('params');

        this.map = null;
        this.markersObj = {};
        this.markersShowedTo = 0;
        this.markersShowedFrom = 0;

        this.box = null;
        this.prevBox = null;
        this.freeDraw = null;

        this.refreshMapTimer = null;
        this.refreshMapTimerDelay = 1000;

        this.resizeTimer = null;
        this.resizeTimerDelay = 300;

        this.markerPopupWidth = 270;
        this.yelpMarkerPopupWidth = 500;

        this.markers = null;
        this.yelpMarkers = null;
        this.schoolsLayers = null;

        this.$estateCardsWrapPosition = this.$estateCardsWrap[0].getBoundingClientRect();

        this.proxy = window.location.hostname === 'estateblock20' ? 'https://cors-anywhere.herokuapp.com/' : '';

        this.templates = {
            markerPopup: ({img, title, text}) => `
                <div class="marker-popup-inner">
                    <div class="marker-popup-inner__img-wrap">
                        <img src=${img} alt="#" class="of"/>
                    </div>
                    
                    <div class="marker-popup-inner__description">
                        <h1>${title}</h1>
                        <p>${text}</p>
                    </div>
                </div>
            `,

            estateCard: ({images, title, text}) => `
                <div class="estate-card js-estate-card">
                    <div class="estate-card__slider js-estate-card-slider">
                        ${images}
                    </div>
                    
                    <div class="estate-card__description">
                        <h1>${title}</h1>
                        <p>${text}</p>
                    </div>
                </div>
            `,

            estateSliderItems: (imagesArray) => imagesArray.map(img => `
                <div class="estate-slider-item">
                    <img data-lazy=${img} src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt="#" class="of js-estate-card-slider-img"/>
                </div>
            `).join(''),

            yelpMarkerPopup: ({display_phone, phone, image_url, displayAddress, name, rating, review_count, url}) => `
                <div class="yelp-marker-popup-inner">
                    <div class="yelp-marker-popup-inner__top">
                        <div class="yelp-marker-popup-inner__description">
                            <h1>${name}</h1>
                            <p>${displayAddress}</p>
                            <a href="tel:${phone}">${display_phone}</a>
                        </div>
                        
                        <div class="yelp-marker-popup-inner__img-wrap">
                            ${image_url && `<img src=${image_url} alt="#" class="of"/>`}
                        </div>
                    </div>
                    
                    <div class="yelp-marker-popup-inner__bottom">
                        <div class="yelp-marker-popup-inner__rating-wrap">
                            <div class="rating _${rating.toString().replace('.','')}"></div>
                            <p>Yelp Rating based on <span class="highlight">${review_count}</span> reviews</p>
                        </div>
                        <div class="yelp-marker-popup-inner__link-wrap">
                            <a href="${url}" target="_blank" class="simple-button">link</a>
                        </div>
                    </div>
                </div>
            `,
        }
    }

    initEvents() {
        this.map.on('move', () => {
            clearTimeout(this.refreshMapTimer);
        });

        this.map.on('moveend', () => {
            this._setBox();
            this._runRefreshTimer();
            console.log(this.map.getZoom());
        });

        this.$estateCardsWrap.on('trigger:check-sliders', () => {
            this._runSliderCheckTimer();
        });

        this.$yelpSelect.on('change', (e) => {
            this.yelpTerm = $(e.currentTarget).val();
            this._yelpSearch();
        });

        this.$showSchools.on('click', (e) => {
            this._loadSchools();
        });

        this.$window.resize(() => {
            this._setParamsOnResize();
        });
    }

    initMap() {
        const
            { center, zoom=13, minZoom=10 } = this.dataParams,

            OpenStreetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }),

            Here = L.tileLayer('https://2.aerial.maps.ls.hereapi.com/maptile/2.1/maptile/newest/satellite.day/{z}/{x}/{y}/512/png8?apiKey={accessToken}', {
                attribution: '&copy; HERE 2019',
                accessToken: 'r8YkmrDOzfX6spDJx1q3azz9rMoIn7zTSNdWIInUzbM'
            }),

            baseMaps = {
                "Streets": OpenStreetMap,
                "Satellite": Here,
            };

        this.map = L.map(this.$map[0], {
            zoom: zoom,
            minZoom: minZoom,
            zoomControl: false,
            layers: [OpenStreetMap],
        }).on('load', () => {
            this._setBox();
            this._runRefreshTimer();
        });

        this.map.setView(center);

        // this._runRefreshTimer()
        // console.log(this.map.getPanes());
        // console.log(this.map.getCenter());

        L.control.layers(baseMaps, null, {
            position: 'bottomright'
        }).addTo(this.map);

        L.control.zoom({
            position: 'bottomright'
        }).addTo(this.map);
    }

    initDrawButtons() {
        const { CREATE, EDIT, DELETE, NONE } = FreeDraw;

        const getDrawCoordinates =(freeDrawAll) => {
            let drawCoordinatesArray = [];

            for (let i = 0; i < freeDrawAll.length; i++) {
                drawCoordinatesArray.push(freeDrawAll[0]._latlngs);
            }

            console.dir(drawCoordinatesArray);
        }

        const toggleDraw = (enable) => {
            this._disableMapInteraction(enable);
            this.$mapContainer[enable ? 'addClass' : 'removeClass']('_show-draw-bar');
            this.freeDraw.mode(enable ? CREATE | EDIT | DELETE : NONE);
        }

        this.$drawButton.on('click', () => {
            if (!this.freeDraw) this.freeDraw = new FreeDraw();

            clearTimeout(this.refreshMapTimer);
            this.markers.clearLayers();
            this.map.addLayer(this.freeDraw);

            toggleDraw(true);
        });

        this.$drawCancel.on('click', () => {
            this.freeDraw.cancel();
            this.freeDraw.clear();
            toggleDraw(false);
            this._runRefreshTimer();
            this.$mapContainer.removeClass('_draw');
        });

        this.$drawApply.on('click', () => {
            getDrawCoordinates(this.freeDraw.all());
            toggleDraw(false);
            this.$mapContainer.addClass('_draw');
        });

        this.$drawEdit.on('click', () => {
            clearTimeout(this.refreshMapTimer);
            toggleDraw(true);
        });
    }

    initSliderCheckTimer() {
        const
            stopScrollingDelay = 100,
            whileScrollingDelay = 200;

        const callTimerFunctions = () =>  {
            this._checkSliders();
        };

        let
            checkIsReady = true,
            scrollTimer = null;

        this._runSliderCheckTimer = () => {
            clearTimeout(scrollTimer);

            scrollTimer = setTimeout(() => {
                callTimerFunctions();
            }, stopScrollingDelay);

            if (checkIsReady) {
                checkIsReady = false;
                callTimerFunctions();

                setTimeout(() => {
                    checkIsReady = true;
                }, whileScrollingDelay)
            }
        }
    }

    _loadSchools() {
        fetch('test-data/school_ca.kml')
            .then(res => res.text())
            .then(kmltext => {
                // Create new kml overlay
                const parser = new DOMParser();
                const kml = parser.parseFromString(kmltext, 'text/xml');
                const track = new L.KML(kml);
                this.map.addLayer(track);

                // Adjust map to show the kml
                const bounds = track.getBounds();
                this.map.fitBounds(bounds);
            });

        // $.ajax(
        //     {
        //         url: 'test-data/school_ca.geojson',
        //         type: 'POST',
        //         dataType: 'json',
        //
        //         success: function (data) {
        //             this._addSchools(data);
        //         },
        //     }
        // );
    }

    _addSchools(data) {
        console.log(data);

        // // Adding Voyager Basemap
        // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}.png', {
        //     maxZoom: 18
        // }).addTo(this.map);
        //
        // // Adding Voyager Labels
        // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}.png', {
        //     maxZoom: 18,
        //     zIndex: 10
        // }).addTo(this.map);
        //


        const client = new carto.Client({
            apiKey: 'e9468a54f5e995d7c036d05d5907e1a22c9cabc4',
            username: 'roefto'
        });
        // const client = new carto.Client({
        //     apiKey: '60a3b14b8d005c59016a3fc25f102899ef3e8141',
        //     username: 'vadimmarusin'
        // });


        const europeanCountriesDataset = new carto.source.Dataset(`
          school_ca
        `);

        // const europeanCountriesDataset = new carto.source.Dataset(`
        //   bc_condo_prices_by_neighbourhood
        // `);

        const europeanCountriesStyle = new carto.style.CartoCSS(`
          #layer {
          polygon-fill: #162945;
            polygon-opacity: 0.5;
            ::outline {
              line-width: 1;
              line-color: #FFFFFF;
              line-opacity: 0.5;
            }
          }
        `);
        const europeanCountries = new carto.layer.Layer(europeanCountriesDataset, europeanCountriesStyle);

        client.addLayers([europeanCountries]);

        client.getLeafletLayer().addTo(this.map);


        //


        // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}.png', {
        //     zIndex: 10
        // }).addTo(this.map);
        //
        //



        // // #########################
        //
        // let
        //     markersArray = [],
        //     areasArray = [];
        //
        // let
        //     cutCoordinates = 0,
        //     totalEmptyCoordinates = 0,
        //     emptyCoordinatesWithoutPolygon = 0,
        //     emptyCoordinatesWithPolygon = 0;
        //
        // for (let i = 0; i < 500; i++) {
        // // for (let i = 0; i < data.features.length; i++) {
        //     const { areas, lat, lon } = data.features[i].properties;
        //
        //
        //     // ####
        //     if (!lat || !lon) {
        //         totalEmptyCoordinates++;
        //
        //         if (areas && areas.length) {
        //             emptyCoordinatesWithPolygon++;
        //         } else {
        //             emptyCoordinatesWithoutPolygon++;
        //         }
        //     }
        //
        //     if ((areas.indexOf("<coordinates>") === -1) || (areas.indexOf("</coordinates>") === -1)) {
        //         cutCoordinates++;
        //     }
        //     // ####
        //
        //     if (lat && lon) {
        //         const marker = L.marker(L.latLng(lat,lon), { icon: this._constructDivIcon({
        //                     mod: 'school'
        //                 })}),
        //             popup = L.responsivePopup().setContent(this.templates.markerPopup({
        //                 img: `https://picsum.photos/300/170??random=${i+1}`,
        //                 title: `title ${i+1}`,
        //                 text: 'test',
        //             }));
        //
        //         marker.bindPopup(
        //             popup,
        //             {
        //                 maxWidth: this.markerPopupWidth,
        //                 closeButton: false,
        //                 riseOnHover: true,
        //                 riseOffset: 9999,
        //                 keepInView: true,
        //                 autoPan: false,
        //             }
        //         );
        //
        //         marker.on('mouseover', () => {
        //             marker.openPopup();
        //         });
        //
        //         marker.on('mouseout', () => {
        //             marker.closePopup();
        //         });
        //
        //         if (areas && areas.length) {
        //             const coordinatesMatches = areas.match("<coordinates>(.*)</coordinates>")
        //
        //             if (coordinatesMatches && coordinatesMatches[1]) {
        //                 const
        //                     coordinates = coordinatesMatches[1].split(' ').map(item => item.split(',')),
        //                     polygon = L.polygon(coordinates, {color: 'red'});
        //
        //                 // markersArray.push(polygon);
        //             }
        //         }
        //
        //         markersArray.push(marker);
        //     }
        // }
        //
        //
        // console.log('cutCoordinates: ', cutCoordinates);
        // console.log('totalEmptyCoordinates: ', totalEmptyCoordinates);
        // console.log('emptyCoordinatesWithoutPolygon: ', emptyCoordinatesWithoutPolygon);
        // console.log('emptyCoordinatesWithPolygon: ', emptyCoordinatesWithPolygon);
        //
        // if (this.schoolsLayers) this.schoolsLayers.clearLayers();
        // this.schoolsLayers = L.layerGroup(markersArray);
        // this.map.addLayer(this.schoolsLayers);
    }

    _constructDivIcon(data={}) {
        const { mod='' } = data;

        return L.divIcon({
            iconSize: [10, 10],
            iconAnchor: [5, 5],
            popupAnchor: [0, 0],
            html: `<div class="marker-inner ${mod}"></div>`,
            className: 'marker-icon',
        });
    }

    _constructYelpDivIcon(data) {
        const { categoriesAliases='', term='' } = data;

        return L.divIcon({
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -15],
            html: `<div class="yelp-marker-inner ${term} ${categoriesAliases}"></div>`,
            className: 'yelp-marker-icon',
        });
    }

    _constructMarkerPopup(data, counter) {
        const { address, lat, lng, mlsNum } = data;

        return L.responsivePopup({
            maxWidth: this.markerPopupWidth,
            closeButton: false,
            riseOnHover: true,
            riseOffset: 9999,
            keepInView: true,
            autoPan: false,
            offset: [0, -15],
        })
            .setLatLng(L.latLng(lat,lng))
            .setContent(this.templates.markerPopup({
                img: `https://picsum.photos/300/170??random=${counter+1}`,
                title: `title ${counter+1}`,
                text: address,
            }))
    }

    _setMarkersObj(data, from=0) {
        const
            { maxMarkersCount=99999 } = this.dataParams,
            to = Math.min(from + maxMarkersCount, data.length);

        this.markersObj = {};
        this.markersShowedTo = to;
        this.markersShowedFrom = from;

        for (let i = from; i < to; i++) {
            const currentItem = data[i];

            if (currentItem) {
                const { lat, lng, mlsNum } = data[i],
                    marker = L.marker(L.latLng(lat,lng), { icon: this._constructDivIcon()});

                let popup = null;

                marker.on('mouseover', () => {
                    if (!popup) popup = this._constructMarkerPopup(data[i], i);
                    this.map.openPopup(popup);
                });

                marker.on('mouseout', () => {
                    if (popup) this.map.closePopup(popup);
                });

                this.markersObj[mlsNum] = marker;
            } else {
                break;
            }
        }
    }

    _parseMarkersData(data) {
        this._setMarkersObj(data);
        this._addMarkers();
        this._initPagination(data);
    }

    _addMarkers(data) {
        if (this.markers) this.markers.clearLayers();
        this.markers = L.layerGroup(Object.values(this.markersObj));
        this.map.addLayer(this.markers);
    }

    _addCards(data, p) {
        let cards = [];

        // TODO: counter - tmp
        const
            {pageNumber, pageSize} = p,
            pageCounter = pageSize * (pageNumber-1);

        for (let i = 0; i < data.length; i++) {
            const images = [
                `https://picsum.photos/300/170??random=${pageCounter+i+1}`,
                `https://picsum.photos/300/170??random=${pageCounter+i+2}`,
                `https://picsum.photos/300/170??random=${pageCounter+i+3}`,
                `https://picsum.photos/300/170??random=${pageCounter+i+4}`,
                `https://picsum.photos/300/170??random=${pageCounter+i+5}`,
                `https://picsum.photos/300/170??random=${pageCounter+i+6}`,
                `https://picsum.photos/300/170??random=${pageCounter+i+7}`,
                `https://picsum.photos/300/170??random=${pageCounter+i+8}`,
                `https://picsum.photos/300/170??random=${pageCounter+i+9}`,
            ];

            const
                { address, mlsNum } = data[i],
                $card = $(this.templates.estateCard({
                    images: this.templates.estateSliderItems(images),
                    title: `title ${pageCounter+i+1}`,
                    text: address,
                }));

            $card.on('mouseenter', () => {
                this.markersObj[mlsNum].fire('mouseover');
                $(this.markersObj[mlsNum]._icon).addClass('_active');
            });

            $card.on('mouseleave', () => {
                this.markersObj[mlsNum].fire('mouseout');
                $(this.markersObj[mlsNum]._icon).removeClass('_active');
            });

            cards.push($card);
        }

        this.$estateCardsList.html('').append(cards);
    }

    _addYelpMarkers(data) {
        const { businesses } = data;
        let yelpMarkersArray = [];

        for (let i = 0; i < businesses.length; i++) {
            const {
                    coordinates,
                    display_phone,
                    phone,
                    image_url,
                    location,
                    name,
                    rating,
                    review_count,
                    url,
                    categories,
                } = businesses[i],

                { latitude, longitude } = coordinates,
                categoriesAliases = categories.map(item => item.alias).join(' '),
                displayAddress = location.display_address.join(', '),

                marker = L.marker(
                    L.latLng(latitude, longitude),
                    { icon: this._constructYelpDivIcon({categoriesAliases, term: this.yelpTerm})}
                ),

                popup = L.responsivePopup().setContent(this.templates.yelpMarkerPopup({
                    display_phone,
                    phone,
                    image_url,
                    displayAddress,
                    name,
                    rating,
                    review_count,
                    url,
                    categoriesAliases,
                }));

            marker.bindPopup(
                popup,
                {
                    maxWidth: this.yelpMarkerPopupWidth,
                    closeButton: true,
                    riseOnHover: true,
                    riseOffset: 9999,
                    keepInView: true,
                    autoPan: false,
                }
            );

            marker.on('mouseover', () => {
                marker.openPopup();
            });

            yelpMarkersArray.push(marker);
        }

        if (this.yelpMarkers) this.yelpMarkers.clearLayers();
        this.yelpMarkers = L.layerGroup(yelpMarkersArray);
        this.map.addLayer(this.yelpMarkers);
    }

    _searchMarkers(box) {
        const
            { path, cardsPreloader } = this.dataParams,
            requestParameters = {
                url: path,
                type: 'POST',
                dataType: 'json',
                data: {box: JSON.stringify(box)}
            };

        if (cardsPreloader) this.$estateCardsWrap.addClass('_loading');

        $.ajax(requestParameters).done((data) => {
            console.log(data);
            this._parseMarkersData(data);
        })
            .fail((err) => {
                console.log(err);
            })
            .always(() => {
                this.$estateCardsWrap.removeClass('_loading');
            })
    }

    _yelpSearch() {
        const
            { yelpPreloader } = this.dataParams,
            mapBounds = this.map.getBounds(),
            center = this.map.getCenter(),
            { lat, lng } = center,
            centerEast = L.latLng(lat, mapBounds.getEast()),
            centerNorth = L.latLng(mapBounds.getNorth(), lng),
            distW = parseInt(center.distanceTo(centerEast)),
            distH = parseInt(center.distanceTo(centerNorth)),
            searchRadius = Math.min(Math.max(distW, distH), 40000),
            queryURL = `${this.proxy}https://api.yelp.com/v3/businesses/search?term=${this.yelpTerm}&latitude=${lat}&longitude=${lng}&radius=${searchRadius}&limit=50`,
            apiKey = "FydBuNrSNr9ZFx6YJm_DxYaeHa6SdFCIdlcZb7Cb8ftPC9O4mMWJ6n1kMCk62g9ZqMbK_N-SLg2DXSl1wrgVOkiM3B1DD-0D_J59KIsR3tlyr07K1Ldx5J7Hjy6tX3Yx",

            requestParameters = {
                url: queryURL,
                method: "GET",
                headers: {
                    "accept": "application/json",
                    "x-requested-with": "xmlhttprequest",
                    "Access-Control-Allow-Origin":"*",
                    "Authorization": `Bearer ${apiKey}`
                },
            };

        if (yelpPreloader) this.$mapContainer.addClass('_loading');

        $.ajax(requestParameters).done((data) => {
            this._addYelpMarkers(data);
        })
            .fail((err) => {
                console.log(err);
            })
            .always(() => {
                this.$mapContainer.removeClass('_loading');
            })
    }

    _runRefreshTimer() {
        clearTimeout(this.refreshMapTimer);

        this.refreshMapTimer = setTimeout(() => {
            if (this.box) {
                if (JSON.stringify(this.box) !== JSON.stringify(this.prevBox)) {
                    this._refreshMap();
                }
            } else {
                this._refreshMap();
            }
        }, this.refreshMapTimerDelay);
    }

    _refreshMap() {
        const { refreshMarkers, refreshYelp } = this.dataParams;

        if (refreshMarkers) this._searchMarkers(this.box);
        if (refreshYelp && this.yelpTerm) this._yelpSearch();
    }

    _initPagination(data) {
        const { cardsPerPage=48, maxMarkersCount=99999 } = this.dataParams;

        this.$estateCardsPagination.pagination({
            dataSource: data,
            pageSize: cardsPerPage,
            pageRange: 1,

            callback: (currentPageData, p) => {
                const
                    { pageNumber, pageSize, direction } = p,
                    pageCounterFrom = pageSize * (pageNumber - 1);

                this.$cardsScrollWrap.trigger('trigger:scroll-top');
                // TODO: p - tmp
                this._addCards(currentPageData, p);
                this._checkSliders();

                if ((this.markersShowedTo <= pageCounterFrom) && (direction === 1)) {
                    this._setMarkersObj(data, pageCounterFrom);
                    this._addMarkers();
                } else if (direction === -1) {
                    if ((pageCounterFrom + pageSize) <= this.markersShowedFrom) {
                        this._setMarkersObj(data, Math.max((pageSize * pageNumber) - maxMarkersCount, 0));
                        this._addMarkers();
                    }
                }
            }
        })
    }


    _setBox() {
        const { _northEast, _southWest } = this.map.getBounds();

        this.prevBox = JSON.parse(JSON.stringify(this.box));

        this.box = {
            northEast: _northEast,
            southWest: _southWest
        }
    }

    _setParamsOnResize() {
        clearTimeout(this.resizeTimer);

        this.resizeTimer = setTimeout(() => {
            this.$estateCardsWrapPosition = this.$estateCardsWrap[0].getBoundingClientRect();
        }, this.resizeTimerDelay);
    }

    _checkSliders() {
        const $sliders = this.$estateCardsList.find('.js-estate-card-slider:not(.slick-initialized)');

        $sliders.each((key, item) => {
            const
                { top } = item.getBoundingClientRect(),
                outOfBottom = top > this.$estateCardsWrapPosition.bottom;

            if (!outOfBottom) {
                const
                    $currentSlider = $(item),
                    isInit = $currentSlider.data('init');

                if (!isInit) {
                    $currentSlider.data('init', true);

                    this.$body.trigger('trigger:init-slider', {
                        $sliders: $currentSlider
                    });
                }
            } else {
                return false;
            }
        })
    }

    _disableMapInteraction(disable) {
        const method = disable ? 'disable' : 'enable';

        this.map.dragging[method]();
        this.map.touchZoom[method]();
        this.map.doubleClickZoom[method]();
        this.map.scrollWheelZoom[method]();
        this.map.boxZoom[method]();
        this.map.keyboard[method]();
        if (this.map.tap) this.map.tap[method]();
    }
}

$(document).ready(() => {
    new EstateMap('#estate-map');

    $('body').on('trigger:init-map', (e, id) => {
        new EstateMap(id);
    });
});
