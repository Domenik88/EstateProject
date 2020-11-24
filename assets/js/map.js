require('leaflet');
require('leaflet-responsive-popup');
require('leaflet-freedraw');
require('leaflet-kml');
const carto = require('@carto/carto.js');
require('paginationjs');

const m_ = {
    init() {
        this.initCache();
        this.initMap();
        // this.initDrawButtons();
        // this.initSliderCheckTimer();
        // this.initEvents();
    },
    
    initCache() {
        this.$body = $('body');
        this.$window = $(window);
        
        this.$mapContainer = $('#estate-map');
        this.$map = $('.js-map');
        this.$mapWrap = $('.js-map-wrap');
        this.$drawButton = $('.js-map-draw');
        this.$drawCancel = $('.js-draw-cancel');
        this.$drawApply = $('.js-draw-apply');
        this.$drawEdit = $('.js-draw-edit');
        this.$showSchools = $('.js-map-show-schools');
        this.$estateCardsWrap = $('.js-estate-cards-wrap');
        this.$estateCardsList = $('.js-estate-cards-list');
        this.$cardsScrollWrap = $('.js-cards-scroll-wrap');
        this.$estateCardsPagination = $('.js-estate-cards-pagination');
        this.$yelpSelect = $('.js-yelp-select');
        
        this.dataParams = m_.$mapContainer.data('params');
    
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
        
        this.$estateCardsWrapPosition = m_.$estateCardsWrap[0].getBoundingClientRect();
        
        this.proxy = window.location.hostname === 'estateblock-local' ? 'https://cors-anywhere.herokuapp.com/' : '';
        
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
    },
    
    initEvents() {
        m_.map.on('move', () => {
            clearTimeout(m_.refreshMapTimer);
        });
        
        m_.map.on('moveend', () => {
            m_._setBox();
            m_._runRefreshTimer();
            console.log(m_.map.getZoom());
        });
    
        m_.$estateCardsWrap.on('trigger:check-sliders', () => {
            m_._runSliderCheckTimer();
        });
        
        m_.$yelpSelect.on('change', (e) => {
            m_.yelpTerm = $(e.currentTarget).val();
            m_._yelpSearch();
        });
        
        m_.$showSchools.on('click', (e) => {
            m_._loadSchools();
        });
    
        m_.$window.resize(() => {
            m_._setParamsOnResize();
        });
    },
    
    initMap() {
        const
            { center, zoom=13, minZoom=10 } = m_.dataParams,
            
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
        
        m_.map = L.map(m_.$mapContainer[0], {
            zoom: zoom,
            minZoom: minZoom,
            zoomControl: false,
            layers: [OpenStreetMap],
        }).on('load', () =>  m_._runRefreshTimer());
        
        m_.map.setView(center);
        
        // m_._runRefreshTimer()
        // console.log(m_.map.getPanes());
        // console.log(m_.map.getCenter());

        L.control.layers(baseMaps, null, {
            position: 'bottomright'
        }).addTo(m_.map);
    
        L.control.zoom({
            position: 'bottomright'
        }).addTo(m_.map);
    },
    
    initDrawButtons() {
        const { CREATE, EDIT, DELETE, NONE } = FreeDraw;
        
        function getDrawCoordinates(freeDrawAll) {
            let drawCoordinatesArray = [];
            
            for (let i = 0; i < freeDrawAll.length; i++) {
                drawCoordinatesArray.push(freeDrawAll[0]._latlngs);
            }
            
            console.dir(drawCoordinatesArray);
        }
        
        function toggleDraw(enable) {
            m_._disableMapInteraction(enable);
            m_.$map[enable ? 'addClass' : 'removeClass']('_show-draw-bar');
            m_.freeDraw.mode(enable ? CREATE | EDIT | DELETE : NONE);
        }
        
        m_.$drawButton.on('click', () => {
            if (!m_.freeDraw) m_.freeDraw = new FreeDraw();
            
            clearTimeout(m_.refreshMapTimer);
            m_.markers.clearLayers();
            m_.map.addLayer(m_.freeDraw);
            
            toggleDraw(true);
        });
        
        m_.$drawCancel.on('click', () => {
            m_.freeDraw.cancel();
            m_.freeDraw.clear();
            toggleDraw(false);
            m_._runRefreshTimer();
            m_.$map.removeClass('_draw');
        });
        
        m_.$drawApply.on('click', () => {
            getDrawCoordinates(m_.freeDraw.all());
            toggleDraw(false);
            m_.$map.addClass('_draw');
        });
        
        m_.$drawEdit.on('click', () => {
            clearTimeout(m_.refreshMapTimer);
            toggleDraw(true);
        });
    },
    
    initSliderCheckTimer() {
        const
            stopScrollingDelay = 100,
            whileScrollingDelay = 200;
        
        let
            checkIsReady = true,
            scrollTimer = null;
        
        function callTimerFunctions() {
            m_._checkSliders();
        }
        
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
    },
    
    _loadSchools() {
    
        fetch('test-data/school_ca.kml')
        .then(res => res.text())
        .then(kmltext => {
            // Create new kml overlay
            const parser = new DOMParser();
            const kml = parser.parseFromString(kmltext, 'text/xml');
            const track = new L.KML(kml);
            m_.map.addLayer(track);
        
            // Adjust map to show the kml
            const bounds = track.getBounds();
            m_.map.fitBounds(bounds);
        });
        
        // $.ajax(
        //     {
        //         url: 'test-data/school_ca.geojson',
        //         type: 'POST',
        //         dataType: 'json',
        //
        //         success: function (data) {
        //             m_._addSchools(data);
        //         },
        //     }
        // );
    },
    
    _addSchools(data) {
        console.log(data);
        
        // // Adding Voyager Basemap
        // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}.png', {
        //     maxZoom: 18
        // }).addTo(m_.map);
		//
        // // Adding Voyager Labels
        // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}.png', {
        //     maxZoom: 18,
        //     zIndex: 10
        // }).addTo(m_.map);
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

	    client.getLeafletLayer().addTo(m_.map);


	    //
	
	
	    // L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}.png', {
		//     zIndex: 10
	    // }).addTo(m_.map);
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
        //         const marker = L.marker(L.latLng(lat,lon), { icon: m_._constructDivIcon({
        //                     mod: 'school'
        //                 })}),
        //             popup = L.responsivePopup().setContent(m_.templates.markerPopup({
        //                 img: `https://picsum.photos/300/170??random=${i+1}`,
        //                 title: `title ${i+1}`,
        //                 text: 'test',
        //             }));
        //
        //         marker.bindPopup(
        //             popup,
        //             {
        //                 maxWidth: m_.markerPopupWidth,
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
        // if (m_.schoolsLayers) m_.schoolsLayers.clearLayers();
        // m_.schoolsLayers = L.layerGroup(markersArray);
        // m_.map.addLayer(m_.schoolsLayers);
    },
    
    _constructDivIcon(data={}) {
        const { mod='' } = data;
        
        return L.divIcon({
            iconSize: [10, 10],
            iconAnchor: [5, 5],
            popupAnchor: [0, 0],
            html: `<div class="marker-inner ${mod}"></div>`,
            className: 'marker-icon',
        });
    },
    
    _constructYelpDivIcon(data) {
        const { categoriesAliases='', term='' } = data;
        
        return L.divIcon({
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -15],
            html: `<div class="yelp-marker-inner ${term} ${categoriesAliases}"></div>`,
            className: 'yelp-marker-icon',
        });
    },
    
    _constructMarkerPopup(data, counter) {
        const { address, lat, lng, mlsNum } = data;
        
        return L.responsivePopup({
            maxWidth: m_.markerPopupWidth,
            closeButton: false,
            riseOnHover: true,
            riseOffset: 9999,
            keepInView: true,
            autoPan: false,
            offset: [0, -15],
        })
        .setLatLng(L.latLng(lat,lng))
        .setContent(m_.templates.markerPopup({
            img: `https://picsum.photos/300/170??random=${counter+1}`,
            title: `title ${counter+1}`,
            text: address,
        }))
    },
    
    _setMarkersObj(data, from=0) {
        const
            { maxMarkersCount=99999 } = m_.dataParams,
            to = Math.min(from + maxMarkersCount, data.length);
        
        m_.markersObj = {};
        m_.markersShowedTo = to;
        m_.markersShowedFrom = from;
    
        for (let i = from; i < to; i++) {
            const currentItem = data[i];
            
            if (currentItem) {
                const { lat, lng, mlsNum } = data[i],
                    marker = L.marker(L.latLng(lat,lng), { icon: m_._constructDivIcon()});
    
                let popup = null;
    
                marker.on('mouseover', () => {
                    if (!popup) popup = m_._constructMarkerPopup(data[i], i);
                    m_.map.openPopup(popup);
                });
    
                marker.on('mouseout', () => {
                    if (popup) m_.map.closePopup(popup);
                });
    
                m_.markersObj[mlsNum] = marker;
            } else {
                break;
            }
        }
    },
    
    _parseMarkersData(data) {
        m_._setMarkersObj(data);
        m_._addMarkers();
        m_._initPagination(data);
    },
    
    _addMarkers(data) {
        if (m_.markers) m_.markers.clearLayers();
        m_.markers = L.layerGroup(Object.values(m_.markersObj));
        m_.map.addLayer(m_.markers);
    },
    
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
                $card = $(m_.templates.estateCard({
                    images: m_.templates.estateSliderItems(images),
                    title: `title ${pageCounter+i+1}`,
                    text: address,
                }));
                
            $card.on('mouseenter', () => {
                m_.markersObj[mlsNum].fire('mouseover');
                $(m_.markersObj[mlsNum]._icon).addClass('_active');
            });
    
            $card.on('mouseleave', () => {
                m_.markersObj[mlsNum].fire('mouseout');
                $(m_.markersObj[mlsNum]._icon).removeClass('_active');
            });
    
            cards.push($card);
        }
        
        m_.$estateCardsList.html('').append(cards);
    },
    
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
                    { icon: m_._constructYelpDivIcon({categoriesAliases, term: m_.yelpTerm})}
                ),
                
                popup = L.responsivePopup().setContent(m_.templates.yelpMarkerPopup({
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
                    maxWidth: m_.yelpMarkerPopupWidth,
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
    
        if (m_.yelpMarkers) m_.yelpMarkers.clearLayers();
        m_.yelpMarkers = L.layerGroup(yelpMarkersArray);
        m_.map.addLayer(m_.yelpMarkers);
    },
    
    _searchMarkers(box) {
        const
            { path, cardsPreloader } = m_.dataParams,
            requestParameters = {
                url: path,
                type: 'POST',
                dataType: 'json',
                data: {box: JSON.stringify(box)}
            };
        
        if (cardsPreloader) m_.$estateCardsWrap.addClass('_loading');
        
        $.ajax(requestParameters).done((data) => {
            console.log(data);
            // m_._parseMarkersData(data);
        })
        .error((err) => {
            console.log(err);
        })
        .always(() => {
            m_.$estateCardsWrap.removeClass('_loading');
        })
    },
    
    _yelpSearch() {
        const
            { yelpPreloader } = m_.dataParams,
            mapBounds = m_.map.getBounds(),
            center = m_.map.getCenter(),
            { lat, lng } = center,
            centerEast = L.latLng(lat, mapBounds.getEast()),
            centerNorth = L.latLng(mapBounds.getNorth(), lng),
            distW = parseInt(center.distanceTo(centerEast)),
            distH = parseInt(center.distanceTo(centerNorth)),
            searchRadius = Math.min(Math.max(distW, distH), 40000),
            queryURL = `${m_.proxy}https://api.yelp.com/v3/businesses/search?term=${m_.yelpTerm}&latitude=${lat}&longitude=${lng}&radius=${searchRadius}&limit=50`,
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
        
        if (yelpPreloader) m_.$mapWrap.addClass('_loading');
        
        $.ajax(requestParameters).done((data) => {
            m_._addYelpMarkers(data);
        })
        .error((err) => {
            console.log(err);
        })
        .always(() => {
            m_.$mapWrap.removeClass('_loading');
        })
    },
    
    _runRefreshTimer() {
        clearTimeout(m_.refreshMapTimer);
        
        m_.refreshMapTimer = setTimeout(() => {
            if (m_.box) {
                if (JSON.stringify(m_.box) !== JSON.stringify(m_.prevBox)) {
                    m_._refreshMap();
                }
            } else {
                m_._refreshMap();
            }
        }, m_.refreshMapTimerDelay);
    },
    
    _refreshMap() {
        const { refreshMarkers, refreshYelp } = m_.dataParams;
        
        if (refreshMarkers) m_._searchMarkers(m_.box);
        if (refreshYelp && m_.yelpTerm) m_._yelpSearch();
    },
    
    _initPagination(data) {
        const { cardsPerPage=48, maxMarkersCount=99999 } = m_.dataParams;
       
        m_.$estateCardsPagination.pagination({
            dataSource: data,
            pageSize: cardsPerPage,
            pageRange: 1,
            
            callback: (currentPageData, p) => {
                const
                    { pageNumber, pageSize, direction } = p,
                    pageCounterFrom = pageSize * (pageNumber - 1);
                
                m_.$cardsScrollWrap.trigger('trigger:scroll-top');
                // TODO: p - tmp
                m_._addCards(currentPageData, p);
                m_._checkSliders();
                
                if ((m_.markersShowedTo <= pageCounterFrom) && (direction === 1)) {
                    m_._setMarkersObj(data, pageCounterFrom);
                    m_._addMarkers();
                } else if (direction === -1) {
                    if ((pageCounterFrom + pageSize) <= m_.markersShowedFrom) {
                        m_._setMarkersObj(data, Math.max((pageSize * pageNumber) - maxMarkersCount, 0));
                        m_._addMarkers();
                    }
                }
            }
        })
    },
    
    
    _setBox() {
        const { _northEast, _southWest } = m_.map.getBounds();
        
        m_.prevBox = JSON.parse(JSON.stringify(m_.box));
        
        m_.box = {
            northEast: _northEast,
            southWest: _southWest
        }
    },
    
    _setParamsOnResize() {
        clearTimeout(m_.resizeTimer);
        
        m_.resizeTimer = setTimeout(() => {
            m_.$estateCardsWrapPosition = m_.$estateCardsWrap[0].getBoundingClientRect();
        }, m_.resizeTimerDelay);
    },
    
    _checkSliders() {
        const $sliders = m_.$estateCardsList.find('.js-estate-card-slider:not(.slick-initialized)');
        
        $sliders.each((key, item) => {
            const
                { top } = item.getBoundingClientRect(),
                outOfBottom = top > m_.$estateCardsWrapPosition.bottom;
            
            if (!outOfBottom) {
                const
                    $currentSlider = $(item),
                    isInit = $currentSlider.data('init');
                
                if (!isInit) {
                    $currentSlider.data('init', true);
                    
                    m_.$body.trigger('trigger:init-slider', {
                        $sliders: $currentSlider
                    });
                }
            } else {
                return false;
            }
        })
    },
    
    _disableMapInteraction(disable) {
        const method = disable ? 'disable' : 'enable';
        
        m_.map.dragging[method]();
        m_.map.touchZoom[method]();
        m_.map.doubleClickZoom[method]();
        m_.map.scrollWheelZoom[method]();
        m_.map.boxZoom[method]();
        m_.map.keyboard[method]();
        if (m_.map.tap) m_.map.tap[method]();
    },
};

$(document).ready(() => {
    m_.init();
});
