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
        this.$schoolsCardsMenu = this.$iw.find('.js-schools-cards-menu');
        this.$homesAvailable = this.$iw.find('.js-homes-available');
        this.$mapLayerOption = this.$iw.find('.js-map-layer-option');
        this.$schoolsOption = this.$iw.find('.js-schools-option');
        this.dataParams = this.$map.data('params');

        this.map = null;
        this.estateCardsShowed = {from: 0, to: 0};
        this.timers = { resize: null, refresh: null };
        this.boxState = { current: null, prev: null };
        this.layerOption = this.$mapLayerOption.length ? this.$mapLayerOption.filter('._active').data('val') : null;
        this.schoolOption = this.$schoolsOption.length ? this.$schoolsOption.filter('._active').data('val') : null;
        this.$estateCardsWrapPosition = this.$estateCardsWrap.length && this.$estateCardsWrap[0].getBoundingClientRect();

        this.layers = {
            listings: { layer: null, items: null, data: null, lastBox: null },
            schools: { layer: null, items: null, data: null, lastBox: null },
            crime: { layer: null, items: null, data: null, lastBox: null },
            commute: { layer: null, items: null, data: null, lastBox: null },
            yelp: { layer: null, items: null, data: null, lastBox: null },
            draw: { layer: null }
        };

        this.settings = {
            refreshDelay: 1000,
            resizeUpdateDelay: 300,
            popupWidth: 400,
            yelpPopupWidth: 290,
        }

        this.selectors = {
            smoothScroll: '.js-smooth-scroll',
        }

        // TODO: REMOVE
        this.proxy = 'https://cors-anywhere.herokuapp.com/';
    }

    initEvents() {
        this.map.on('move', () => {
            clearTimeout(this.timers.refresh);
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
            this._loadSchools();
        });

        this.$schoolsOption.add(this.$mapLayerOption).on('click trigger:click', (e) => {
            const
                $currentTarget = $(e.currentTarget),
                isActive = $currentTarget.data('active-option');

            if (!isActive) {
                const val = $currentTarget.data('val');

                this._clearLayers();

                if ($currentTarget.is(this.$schoolsOption)) {
                    this.$schoolsOption.data('active-option', false);
                    this.schoolOption = val;
                    this._loadSchools();
                }

                if ($currentTarget.is(this.$mapLayerOption)) {
                    this.$mapLayerOption.data('active-option', false);
                    this.layerOption = val;
                    this._runRefreshTimer(100);
                }

                $currentTarget.data('active-option', true);
            }
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
            // this._setBox();
            // this._yelpSearch();
        }

        this.$yelpNavLink.on('click', (e) => {
            this.yelpTerm = $(e.currentTarget).data('val');
            this._yelpSearch();
        });
    }

    initDrawButtons() {
        const { CREATE, EDIT, DELETE, NONE } = FreeDraw;

        const getDrawCoordinates = (freeDrawAll) => {
            let drawCoordinatesArray = [];

            for (let i = 0; i < freeDrawAll.length; i++) {
                drawCoordinatesArray.push(freeDrawAll[0]._latlngs);
            }

            console.dir(drawCoordinatesArray);
        };

        const toggleDraw = (enable) => {
            this._disableMapInteraction(enable);
            this.$iw[enable ? 'addClass' : 'removeClass']('_drawing');
            this.layers.draw.layer.mode(enable ? CREATE | EDIT | DELETE : NONE);
        };
        
        this._cancelDraw = () => {
            if (this.layers.draw.layer) {
                this.layers.draw.layer.cancel();
                this.layers.draw.layer.clear();
                toggleDraw(false);
                this._runRefreshTimer();
                this.$iw.removeClass('_draw-apply');
                this.layers.draw.layer = null;
            }
        }

        this.$drawButton.on('click', () => {
            if (!this.layers.draw.layer) this.layers.draw.layer = new FreeDraw();

            clearTimeout(this.timers.refresh);
            this._clearLayers('draw');
            this.map.addLayer(this.layers.draw.layer);
            toggleDraw(true);
        });

        this.$drawCancel.on('click', () => {
            this._cancelDraw();
        });

        this.$drawApply.on('click', () => {
            getDrawCoordinates(this.layers.draw.layer.all());
            toggleDraw(false);
            this.$iw.addClass('_draw-apply');
        });

        this.$drawEdit.on('click', () => {
            clearTimeout(this.timers.refresh);
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
        const
            box = this.boxState.current,
            { schoolsPath } = this.dataParams,
            requestParameters = {
                url: schoolsPath,
                type: 'POST',
                dataType: 'json',
                data: {box: JSON.stringify(box)}
            };

        const request = $.ajax(requestParameters).done((data) => {
            this._parseSchoolsData(data);
            this._addSchools();
            this._addCardsToMapMenu({
                $wrap: this.$schoolsCardsMenu,
                data: this.layers.schools.data,
                layer: 'schools',
                template: 'schoolCard',
            });
        });

        this._showPreloader(request, 'schools');
        this._errorHandler(request);
    }

    _parseSchoolsData(data) {
        this.layers.schools.items = {};
        this.layers.schools.data = [];

        for (let i = 0; i < data.length; i++) {
            const
                { level } = data[i],
                matchOption = level.toLowerCase().indexOf(this.schoolOption) !== -1;

            if (!this.schoolOption || (this.schoolOption === 'all') || matchOption) {
                const
                    { areas, coordinates } = data[i],
                    { latitude, longitude } = coordinates,
                    id = this._coordinatesToId(coordinates),
                    marker = L.marker(
                        L.latLng(latitude,longitude),
                        {
                            icon: this._constructDivIcon({ mod: '_school' })
                        }
                    );

                let popup = null;

                marker.on('mouseover', () => {
                    if (!popup) popup = this._constructMarkerPopup({
                        data: data[i],
                        template: 'schoolMarkerPopup',
                        // width: '',
                    });
                    this.map.openPopup(popup);
                });

                if (areas) {
                    this.layers.schools.items[`${id}_polygon`] = L.polygon(areas.map(item => Object.values(item)), {
                        color: 'red',
                        weight: 0,
                        interactive: false,
                        bubblingMouseEvents: false,
                    });
                }

                this.layers.schools.data.push(data[i]);
                this.layers.schools.items[id] = marker;
            }
        }
    }

    _addSchools() {
        if (this.layers.schools.layer) this.layers.schools.layer.clearLayers();
        this.layers.schools.layer = L.layerGroup(Object.values(this.layers.schools.items));
        this.map.addLayer(this.layers.schools.layer);
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

    _constructMarkerPopup(obj) {
        const
            { data, template, width } = obj,
            { coordinates } = data,
            { lat, latitude, lng, longitude } = coordinates;

        return L.responsivePopup({
            maxWidth: width || this.settings.popupWidth,
            closeButton: true,
            riseOnHover: true,
            riseOffset: 9999,
            keepInView: true,
            autoPan: false,
            offset: [0, -15],
        })
            .setLatLng(L.latLng(lat||latitude, lng||longitude))
            .setContent(mapTemplates[template](data))
    }

    _parseListingsData(data, from=0) {
        const
            { maxMarkersCount=4800 } = this.dataParams,
            to = Math.min(from + maxMarkersCount, data.length);

        this.layers.listings.items = {};
        this.estateCardsShowed = { from, to };

        for (let i = from; i < to; i++) {
            if (data[i]) {
                const
                    { coordinates, mlsNumber } = data[i],
                    { lat, lng } = coordinates,
                    marker = L.marker(L.latLng(lat,lng), {
                        icon: this._constructDivIcon(),

                        // TODO: upper yelp?
                        // zIndexOffset: 100,
                    });

                let popup = null;

                marker.on('mouseover', () => {
                    if (!popup) popup = this._constructMarkerPopup({
                        data: data[i], 
                        template: 'listingMarkerPopup',
                    });
                    this.map.openPopup(popup);
                });

                this.layers.listings.items[mlsNumber] = marker;
            } else {
                break;
            }
        }
    }

    _setAvailableHomes(count) {
        this.$homesAvailable.html(_formatCurrency(count));
    }

    _addListings() {
        if (this.layers.listings.layer) this.layers.listings.layer.clearLayers();
        this.layers.listings.layer = L.layerGroup(Object.values(this.layers.listings.items));
        this.map.addLayer(this.layers.listings.layer);
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

    _addCards(data) {
        let cards = [];

        for (let i = 0; i < data.length; i++) {
            const
                { mlsNumber } = data[i],
                $card = $(mapTemplates.estateCard(data[i]));
            
            this._bindCardMouseEvents($card, this.layers.listings.items[mlsNumber]);
            cards.push($card);
        }

        this.$estateCardsList.html('').append(cards);
    }

    _parseYelpMarkers(data) {
        const { businesses } = data;
        this.layers.yelp.items = {};

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
                        maxWidth: this.settings.yelpPopupWidth,
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

                this.layers.yelp.items[id] = marker;
            }
        }
    }

    _addYelpMarkers() {
        if (this.layers.yelp.layer) this.layers.yelp.layer.clearLayers();
        this.layers.yelp.layer = L.layerGroup(Object.values(this.layers.yelp.items));
        this.map.addLayer(this.layers.yelp.layer);
    }

    _addSchoolsCardsToMapMenu(data) {
        if (this.$schoolsCardsMenu.length) {
            const

                $closestScroll = this.$yelpCardsMenu.closest(this.selectors.smoothScroll);

            let schoolsCardsArray = [];

            for (let i = 0; i < data.length; i++) {
                const
                    { id } = data[i],
                    $card = $(mapTemplates.yelpSimpleCard(data[i]));

                this._bindCardMouseEvents($card, this.layers.schools.items[id]);
                schoolsCardsArray.push($card);
            }

            this.$yelpCardsMenu.html('').append(schoolsCardsArray);
            $closestScroll.trigger('trigger:scroll-top');
        }
    }

    _addCardsToMapMenu(obj) {
        const { $wrap, data, template, layer } = obj;

        if ($wrap.length) {
            const $closestScroll = $wrap.closest(this.selectors.smoothScroll);

            let cardsArray = [];

            for (let i = 0; i < data.length; i++) {
                const
                    { id, coordinates } = data[i],
                    findId = id || this._coordinatesToId(coordinates),
                    $card = $(mapTemplates[template](data[i]));

                this._bindCardMouseEvents($card, this.layers[layer].items[findId]);
                cardsArray.push($card);
            }

            $wrap.html('').append(cardsArray);
            $closestScroll.trigger('trigger:scroll-top');
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

                this._bindCardMouseEvents($card, this.layers.yelp.items[id]);
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

    _loadListings() {
        const
            box = this.boxState.current,
            { path } = this.dataParams,
            requestParameters = {
                url: path,
                type: 'POST',
                dataType: 'json',
                data: {box: JSON.stringify(box)}
            };

        const request = $.ajax(requestParameters).done((data) => {
            this._setAvailableHomes(data.length);
            this._parseListingsData(data);
            this._addListings();
            if (this.$estateCardsPagination.length) this._initPagination(data);
        });

        this._showPreloader(request, 'listing');
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
            term = this.yelpTerm || 'all',
            queryURL = `${this.proxy}https://api.yelp.com/v3/businesses/search?term=${term}&latitude=${lat}&longitude=${lng}&radius=${searchRadius}&limit=50`,
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
            this._parseYelpMarkers(data);
            this._addYelpMarkers();
            this._addYelpCardsToSlider(data);

            this._addCardsToMapMenu({
                $wrap: this.$yelpCardsMenu,
                data: data.businesses,
                layer: 'yelp',
                template: 'yelpSimpleCard',
            });
        });

        this._showPreloader(request, 'yelp');
        this._errorHandler(request);
    }

    _runRefreshTimer(delay) {
        clearTimeout(this.timers.refresh);

        this.timers.refresh = setTimeout(() => {
            const { current, prev } = this.boxState;

            if (current) {
                if (JSON.stringify(current) !== JSON.stringify(prev)) {
                    this._refreshMap();
                }
            } else {
                this._refreshMap();
            }
        }, delay || this.settings.refreshDelay);
    }

    _isBoxChanged(layer) {
        const changed = this.boxState.current !== layer.lastBox;
        layer.lastBox = this.boxState.current;
        return changed;
    }

    _refreshMap() {
        const
            { refreshLayers } = this.dataParams,
            refreshArray = JSON.parse(JSON.stringify(refreshLayers));

        if (refreshArray.indexOf(this.layerOption) === -1) refreshArray.push(this.layerOption);

        // TODO: prevent close popup if marker exist?
        this.map.closePopup();

        refreshArray.forEach(item => {
            switch (item) {
                case 'listings':
                    if (this._isBoxChanged(this.layers.listings)) this._loadListings();
                    break;

                case 'schools':
                    if (this._isBoxChanged(this.layers.schools)) {
                        this._loadSchools();
                    } else {
                        this._addSchools(this.layers.schools.items);
                    }
                    break;

                case 'yelp':
                    if (this._isBoxChanged(this.layers.yelp)) {
                        this._yelpSearch();
                    } else {
                        this._addYelpMarkers();
                    }
                    break;
            }
        });
    }

    _initPagination(data) {
        const { cardsPerPage=48, maxMarkersCount=2400 } = this.dataParams;

        this.$estateCardsPagination.pagination({
            dataSource: data,
            pageSize: cardsPerPage,
            pageRange: 1,
            hideWhenLessThanOnePage: true,

            callback: (currentPageData, p) => {
                const
                    { pageNumber, pageSize, direction } = p,
                    pageCounterFrom = pageSize * (pageNumber - 1),
                    { from, to } = this.estateCardsShowed;

                this.$cardsScrollWrap.trigger('trigger:scroll-top');

                if ((to <= pageCounterFrom) && (direction === 1)) {
                    this._parseListingsData(data, pageCounterFrom);
                    this._addListings();
                } else if (direction === -1) {
                    if ((pageCounterFrom + pageSize) <= from) {
                        this._parseListingsData(data, Math.max((pageSize * pageNumber) - maxMarkersCount, 0));
                        this._addListings();
                    }
                }

                this._addCards(currentPageData);
                this._checkSliders();
            }
        })
    }


    _setBox() {
        const { _northEast, _southWest } = this.map.getBounds();

        if (this.boxState.current) this.boxState.prev = JSON.parse(JSON.stringify(this.boxState.current));

        this.boxState.current = {
            northEast: _northEast,
            southWest: _southWest
        }
    }

    _setParamsOnResize() {
        clearTimeout(this.timers.resize);

        this.timers.resize = setTimeout(() => {
            if (this.$estateCardsWrap.length) {
                this.$estateCardsWrapPosition = this.$estateCardsWrap[0].getBoundingClientRect();
            }
        }, this.settings.resizeUpdateDelay);
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

    _showPreloader(request, name='request') {
        // TODO: animate?

        const
            startTime = $.now(),
            mod = `_loading-${name}`;

        this.$mapContainer.addClass(mod);

        request.always(() => {
            // this.$mapContainer.removeClass(mod);

            setTimeout(() => {
                this.$mapContainer.removeClass(mod);
            }, Math.max(0, 1300 - ($.now() - startTime)))
        });
    }

    _errorHandler(request) {
        request.fail((err) => {
            console.log(err);
        });
    }

    _clearLayers(exception='') {
        const { preventClear } = this.dataParams;

        this.map.closePopup();

        for (let key in this.layers) {
            const
                currentLayer = this.layers[key],
                isException = key === exception,
                prevent = preventClear.indexOf(key) !== -1;

            if (currentLayer && !isException && !prevent) {
                if (key === 'draw') {
                    this._cancelDraw();
                } else {
                    const { layer } = currentLayer;
                    if (layer) currentLayer.layer.clearLayers();
                }
            }
        }
    }

    _coordinatesToId(coordinates) {
        return _getPureNumber(JSON.stringify(Object.values(coordinates))).split('.').join('');
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
