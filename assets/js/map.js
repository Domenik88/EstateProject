require('leaflet');
require('leaflet-responsive-popup');
require('leaflet-freedraw');
require('leaflet-kml');
const carto = require('@carto/carto.js');
require('paginationjs');
import { mapTemplates } from './templates/map-templates';

class EstateMap {
    constructor(id) {
        this.initCache(id);
        this.initMap();
        this.initDrawButtons();
        this.initSliderCheckTimer();
        this.initEvents();
        this.initYelpNav();
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
        this.$yelpNavLink = this.$iw.find('.js-yelp-nav-link');
        this.$yelpCardsSlider = this.$iw.find('.js-yelp-cards-slider');
        this.$yelpCardsMenu = this.$iw.find('.js-yelp-cards-menu');

        this.$homesAvailable = this.$iw.find('.js-homes-available');

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

        this.markerPopupWidth = 400;
        this.yelpMarkerPopupWidth = 290;

        this.markers = null;
        this.yelpMarkersObj = null;
        this.yelpMarkers = null;

        this.schoolsLayers = null;

        this.$estateCardsWrapPosition = this.$estateCardsWrap.length && this.$estateCardsWrap[0].getBoundingClientRect();

        // TODO: REMOVE
        this.proxy = 'https://cors-anywhere.herokuapp.com/';
    }

    initEvents() {
        this.map.on('move', () => {
            clearTimeout(this.refreshMapTimer);
        });

        this.map.on('moveend', () => {
            this._setBox();
            this._runRefreshTimer();
        });

        this.$estateCardsWrap.on('trigger:check-sliders', () => {
            this._runSliderCheckTimer();
        });

        this.$yelpSelect.on('change', (e) => {
            this.yelpTerm = $(e.currentTarget).val();
            this._yelpSearch();
        });

        this.$showSchools.on('click', (e) => {
            this._loadSchools(this.box);
        });

        this.$window.resize(() => {
            this._setParamsOnResize();
        });
    }

    initMap() {
        const
            { center, zoom=13, minZoom=10, initialMarker, disableControls=false } = this.dataParams,

            OpenStreetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }),

            Here = L.tileLayer('https://2.aerial.maps.ls.hereapi.com/maptile/2.1/maptile/newest/satellite.day/{z}/{x}/{y}/512/png8?apiKey={accessToken}', {
                attribution: '&copy; HERE 2019',
                accessToken: 'r8YkmrDOzfX6spDJx1q3azz9rMoIn7zTSNdWIInUzbM',
            }),

            baseMaps = {
                "Streets": OpenStreetMap,
                "Satellite": Here,
            };

        OpenStreetMap.on('load', () => {
            this.$map.trigger('trigger:open-street-map-lite-loaded');
        });

        Here.on('load', () => {
            this.$map.trigger('trigger:here-lite-loaded');
        });

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

        if (initialMarker) {
            L.marker(
                L.latLng(center.lat,center.lng),
                {
                    icon: this._constructYelpDivIcon({
                        term: 'realestate'
                    }),
                    zIndexOffset: 99999
                }
            ).addTo(this.map);
        }

        if (!disableControls) {
            L.control.layers(baseMaps, null, {
                position: 'bottomright'
            }).addTo(this.map);

            L.control.zoom({
                position: 'bottomright'
            }).addTo(this.map);
        }

        this.$map.addClass('map-initialized')
    }

    initYelpNav() {
        const $activeNavLink = this.$yelpNavLink.filter('._active');

        if ($activeNavLink.length) {
            this.yelpTerm = $activeNavLink.data('val');
            this._setBox();
            this._yelpSearch();
        }

        this.$yelpNavLink.on('click', (e) => {
            this.yelpTerm = $(e.currentTarget).data('val');
            this._yelpSearch();
        });
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

    _loadSchools(box) {
        const
            { schoolsPath } = this.dataParams,
            requestParameters = {
                url: schoolsPath,
                type: 'POST',
                dataType: 'json',
                data: {box: JSON.stringify(box)}
            };

        const request = $.ajax(requestParameters).done((data) => {
            this._addSchools(data);
        });

        this._showPreloader(request);
        this._errorHandler(request);
    }

    _addSchools(data) {
        console.log(data);
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
        const { categories=[], term='' } = data;

        return L.divIcon({
            iconSize: [42, 49],
            iconAnchor: [21, 49],
            popupAnchor: [0, -49],
            html: `<div class="yelp-marker-inner icon-${term} ${categories.map(item => item.alias).join(' ')}"></div>`,
            className: 'yelp-marker-icon',
        });
    }

    _constructMarkerPopup(data, counter) {
        const
            { coordinates } = data,
            { lat, lng } = coordinates;

        return L.responsivePopup({
            maxWidth: this.markerPopupWidth,
            closeButton: true,
            riseOnHover: true,
            riseOffset: 9999,
            keepInView: true,
            autoPan: false,
            offset: [0, -15],
        })
            .setLatLng(L.latLng(lat,lng))
            .setContent(mapTemplates.markerPopup(data))
    }

    _setMarkersObj(data, from=0) {
        const
            { maxMarkersCount=99999 } = this.dataParams,
            to = Math.min(from + maxMarkersCount, data.length);

        this.markersObj = {};
        this.markersShowedTo = to;
        this.markersShowedFrom = from;

        for (let i = from; i < to; i++) {
            if (data[i]) {
                const
                    { coordinates, mlsNumber } = data[i],
                    { lat, lng } = coordinates,
                    marker = L.marker(L.latLng(lat,lng), { icon: this._constructDivIcon()});

                let popup = null;

                marker.on('mouseover', () => {
                    if (!popup) popup = this._constructMarkerPopup(data[i], i);
                    this.map.openPopup(popup);
                });

                this.markersObj[mlsNumber] = marker;
            } else {
                break;
            }
        }
    }

    _parseMarkersData(data) {
        this._setAvailableHomes(data.length);
        this._setMarkersObj(data);
        this._addMarkers();
        if (this.$estateCardsPagination.length) this._initPagination(data);
    }

    _setAvailableHomes(count) {
        this.$homesAvailable.html(_formatCurrency(count));
    }

    _addMarkers(data) {
        if (this.markers) this.markers.clearLayers();
        this.markers = L.layerGroup(Object.values(this.markersObj));
        this.map.addLayer(this.markers);
    }
    
    _bindCardMouseEvents($item, $marker) {
        $item.on('mouseenter', () => {
            $marker.fire('mouseover');
            $($marker._icon).addClass('_active');
        });

        $item.on('mouseleave', () => {
            $marker.fire('mouseout');
            $($marker._icon).removeClass('_active');
        });
    }

    _addCards(data, p) {
        let cards = [];

        // TODO: counter - tmp
        const
            {pageNumber, pageSize} = p,
            pageCounter = pageSize * (pageNumber-1);

        for (let i = 0; i < data.length; i++) {
            const testData = {
                images: [
                    `https://picsum.photos/300/170??random=${pageCounter+i+1}`,
                    `https://picsum.photos/300/170??random=${pageCounter+i+2}`,
                    `https://picsum.photos/300/170??random=${pageCounter+i+3}`,
                    `https://picsum.photos/300/170??random=${pageCounter+i+4}`,
                    `https://picsum.photos/300/170??random=${pageCounter+i+5}`,
                    `https://picsum.photos/300/170??random=${pageCounter+i+6}`,
                    `https://picsum.photos/300/170??random=${pageCounter+i+7}`,
                    `https://picsum.photos/300/170??random=${pageCounter+i+8}`,
                    `https://picsum.photos/300/170??random=${pageCounter+i+9}`,
                ],

                isNew: true,
                forSaleByOwner: true,
                favoritePath: '#',
                loginHref: '#',
                listingPrice: 849888.88,
                address: {
                    country: "Canada",
                    state: "British Columbia",
                    city: "Kelowna",
                    postalCode: "V1X3B1",
                    streetAddress: "285 Rutland Road, N",
                },
                metrics: {
                    yearBuilt: null,
                    bedRooms: null,
                    bathRooms: 2,
                    stories: 1,
                    lotSize: null,
                    lotSizeUnits: "acres",
                    sqrtFootage: null,
                    sqrtFootageUnits: "square feet",
                },
                mlsNumber: '10202897',
            };

            const
                { mlsNumber } = data[i],
                $card = $(mapTemplates.estateCard(data[i]));
            
            this._bindCardMouseEvents($card, this.markersObj[mlsNumber]);
            cards.push($card);
        }

        this.$estateCardsList.html('').append(cards);
    }

    _addYelpMarkers(data) {
        const { businesses } = data;
        this.yelpMarkersObj = {};

        for (let i = 0; i < businesses.length; i++) {
            const {
                    id,
                    coordinates,
                    image_url,
                    name,
                    rating,
                    review_count,
                    url,
                    categories,
                } = businesses[i],

                { latitude, longitude } = coordinates;

            if (latitude && longitude) {
                const
                    marker = L.marker(
                        L.latLng(latitude, longitude),
                        { icon: this._constructYelpDivIcon({categories, term: this.yelpTerm})}
                    ),

                    popup = L.responsivePopup().setContent(mapTemplates.yelpMarkerPopup({
                        name,
                        rating,
                        review_count,
                        image_url,
                        categories,
                        url
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
                        hasTip: false,
                    }
                );

                marker.on('mouseover', () => {
                    marker.openPopup();
                });

                this.yelpMarkersObj[id] = marker;
            }
        }

        if (this.yelpMarkers) this.yelpMarkers.clearLayers();
        this.yelpMarkers = L.layerGroup(Object.values(this.yelpMarkersObj));
        this.map.addLayer(this.yelpMarkers);
    }

    _addYelpCardsToMapMenu(data) {
        if (this.$yelpCardsMenu.length) {
            const { businesses } = data;
            let yelpSimpleCardsArray = [];

            for (let i = 0; i < businesses.length; i++) {
                const
                    { id } = businesses[i],
                    $card = $(mapTemplates.yelpSimpleCard(businesses[i]));

                this._bindCardMouseEvents($card, this.yelpMarkersObj[id]);
                yelpSimpleCardsArray.push($card);
            }

            this.$yelpCardsMenu.html('').append(yelpSimpleCardsArray);
        }
    }
    
    _addYelpCardsToSlider(data) {
        if (this.$yelpCardsSlider.length) {
            const { businesses } = data;
            let yelpCardsArray = [];

            for (let i = 0; i < businesses.length; i++) {
                const
                    { id } = businesses[i],
                    $card = $(mapTemplates.yelpCard(businesses[i]));

                this._bindCardMouseEvents($card, this.yelpMarkersObj[id]);
                yelpCardsArray.push($card);
            }

            this.$body.trigger('trigger:init-slider', {
                $sliders: this.$yelpCardsSlider,
                $slides: [yelpCardsArray],
                sliderParams: {
                    slidesToShow: 5,
                    slidesToScroll: 5,
                }
            });
        }
    }

    _searchMarkers(box) {
        const
            { path } = this.dataParams,
            requestParameters = {
                url: path,
                type: 'POST',
                dataType: 'json',
                data: {box: JSON.stringify(box)}
            };

        const request = $.ajax(requestParameters).done((data) => {
            this._parseMarkersData(data);
        });

        this._showPreloader(request);
        this._errorHandler(request);
    }

    _yelpSearch() {
        const
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

        const request = $.ajax(requestParameters).done((data) => {
            this._addYelpMarkers(data);
            this._addYelpCardsToSlider(data);
            this._addYelpCardsToMapMenu(data);
        });

        this._showPreloader(request);
        this._errorHandler(request);
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
            hideWhenLessThanOnePage: true,

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
            if (this.$estateCardsWrap.length) {
                this.$estateCardsWrapPosition = this.$estateCardsWrap[0].getBoundingClientRect();
            }
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

    _showPreloader(request) {
        const startTime = $.now();

        this.$mapContainer.addClass('_loading');

        request.always(() => {
            setTimeout(() => {
                this.$mapContainer.removeClass('_loading');
            }, Math.max(0, 1300 - ($.now() - startTime)))
        });
    }

    _errorHandler(request) {
        request.fail((err) => {
            console.log(err);
        });
    }
}

$(document).ready(() => {
    console.log('### MAP ###');

    if ($('#estate-map').length) new EstateMap('#estate-map');

    $('body').on('trigger:init-map', (e, id) => {
        const $map = $(id);
        if ($map.length && !$map.hasClass('map-initialized')) new EstateMap(id);
    });
});
