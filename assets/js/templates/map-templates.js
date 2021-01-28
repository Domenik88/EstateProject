export const mapTemplates = {
    markerPopup: ({
        images,
        financials,
        address,
        metrics,
        mlsNumber,
    }) => `
        <div class="marker-popup-inner">
            <div class="marker-popup-inner__left-col">
                <a class="marker-popup-inner__img-wrap mb5" href="#">
                    ${images && images[0] 
                        ? `<img src=${images[0]} alt="#" class="marker-popup-inner__img of"/>` 
                        : `<div class="marker-popup-inner__img of default-img-bg"></div>`
                    }
                </a>
                
                ${financials && financials.listingPrice ?
                    `<span class="tiny-text_bold">${_formatCurrencyCa(financials.listingPrice)}</span>`
                : ''}   
            </div>
            
            <div class="marker-popup-inner__right-col">
                ${(address && address.streetAddress && address.city) ?
                    `<a class="marker-popup-inner__title link-dark h5 mb5" href="#">${address.streetAddress + ', ' + address.city}</a>`
                : ''}
                
                ${metrics ? `
                    <div class="mb5">
                        ${mapTemplates.metrics(metrics)}
                    </div>
                ` : ''}
                
                ${mlsNumber ?
                    `<span class="gray-mls-after icon-mls-min tiny-text">${mlsNumber}</span>`
                : ''}
            </div>
        </div>
    `,

    estateCard: ({
         images,
         isNew,
         forSaleByOwner,
         listingId,
         userFavorite,
         financials,
         address,
         metrics,
         mlsNumber,
    }) =>{
        const
            favoriteMod = IS_AUTHENTICATED_REMEMBERED ? 'js-favorite-listing' : 'js-call-popup',
            favoriteDataAttribute = IS_AUTHENTICATED_REMEMBERED ?
                `data-url="${ADD_TO_FAVORITES_PATH.replace('@', listingId)}"` :
                `data-popup=${JSON.stringify({ target: 'authorization', show_overlay: true })}`;

        return `
            <a class="estate-card _small _transparent-controls js-estate-card" href="#">
                <div class="estate-card__slider-wrap js-wrap">
                    <div
                        class="estate-cards-slider js-estate-card-slider"
                        data-lazy-inner="true"
                        data-img-selector="ec-src"
                        data-slider-parameters=${JSON.stringify({ lazyLoad: 'ondemand', dots: true })}
                    >
                        ${mapTemplates.estateSliderItems(images)}
                    </div>
                    
                    <div class="estate-card__header">
                        <div class="estate-card__labels-wrap">
                            ${isNew ? `<span class="estate-card__label schild_2">NEW</span>` : ''}
                            ${forSaleByOwner ? `<span class="estate-card__label schild_2">for sale by owner</span>` : ''}
                        </div>
            
                        <span
                            class="estate-card__add-to-favorite circle-button _ic-fs-12 favorite-toggle ${userFavorite ? '_active' : '' } ${favoriteMod} js-prevent"
                            ${favoriteDataAttribute}
                        ></span>
                    </div>
                    
                    <div class="estate-card__controls-wrap js-slider-nav">
                        <span class="estate-card__arrow circle-button _bordered icon-angle-left js-arrow-left js-prevent"></span>
                        <span class="estate-card__arrow circle-button _bordered icon-angle-right js-arrow-right js-prevent"></span>
                    </div>
                </div>
                
                <div class="estate-card__description pt10 pb20">
                    ${financials && financials.listingPrice ? 
                        `<span class="estate-card__title body-text_bold mb5">${_formatCurrencyCa(financials.listingPrice)}</span>`
                    : ''}
                  
                    ${(address && address.streetAddress && address.city) ? 
                        `<span class="estate-card__location h6 mb5">${address.streetAddress + ', ' + address.city}</span>` 
                    : ''}
                    
                    ${metrics ? `
                        <div class="estate-card__metrics-wrap mb10">
                            ${mapTemplates.metrics(metrics)}
                        </div>
                    ` : ''}
                    
                    ${mlsNumber ? 
                        `<span class="gray-mls-after icon-mls-min tiny-text">${'MLS® ' + mlsNumber}</span>`
                    : ''}
                </div>
            </a>
        `
    },

    estateSliderItems: (imagesArray) => {
        if (imagesArray && imagesArray.length) {
            return imagesArray.map(img => `
                <div class="estate-cards-slider__item">
                    <img class="estate-cards-slider__img of" data-ec-src=${img} src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt="#" />
                </div>
            `).join('')
        } else {
            return `<div class="estate-cards-slider__item default-img-bg"></div>`
        }
    },

    metrics: (metrics) => {
        const
            metricsLabels = {
                bedRooms: {
                    suffix: 'Beds',
                },
                bathRooms: {
                    suffix: 'Baths',
                },
                stories: {
                    suffix: 'Stories',
                },
                lotSize: {
                    prefix: 'lot',
                    suffix: metrics.lotSizeUnits || 'sqft',
                },
                sqrtFootage: {
                    suffix: metrics.sqrtFootageUnits || 'sqft',
                },
                yearBuilt: {
                    suffix: 'built',
                }
            },
            outputProps = ['bedRooms','bathRooms','stories','lotSize','sqrtFootage','yearBuilt'];

        let metricsItems = [];

        for (let i = 0; i < outputProps.length; i++) {
            const val = metrics[outputProps[i]];

            if (val) {
                const
                    label = metricsLabels[outputProps[i]],
                    prefix = label && label.prefix,
                    suffix = label && label.suffix;

                metricsItems.push(`
                    <div class="metrics__item">
                        ${prefix ? `<span class="metrics__label small-text">${prefix}</span>` : ''}
                        <span class="metrics__val small-text_bold">${val}</span>
                        ${suffix ? `<span class="metrics__label small-text">${suffix}</span>` : ''}
                    </div>
                `);
            }
        }

        return `
            <div class="metrics _simple">
                ${metricsItems.join('')}
            </div>
        `
    },

    yelpMarkerPopup: ({
        name,
        rating,
        review_count,
        image_url,
        categories,
        url,
    }) => `
        <div class="yelp-marker-popup-inner">
            <a class="yelp-marker-popup-inner__img-wrap ${!image_url && 'default-img-bg-small'}" href="${url}" target="_blank">
                ${image_url ? `<img src=${image_url} alt="#" class="of"/>` : ''}
            </a>
            
            <div class="yelp-marker-popup-inner__info">
                <div class="yelp-marker-popup-inner__description">
                    <a class="yelp-marker-popup-inner__title link-dark h6" href="${url}" target="_blank">${name}</a>
                    <span class="yelp-marker-popup-inner__categories tiny-text">${categories.map(item => item.title).join(', ')}</span>
                </div>
                
                <div class="yelp-marker-popup-inner__rating-wrap">
                    <div class="rating _${rating.toString().replace('.','')}"></div>
                    
                    <div class="yelp-marker-popup-inner__reviews very-tiny">
                        <span>${review_count}</span>
                        <span>&nbsp;Reviews</span>
                    </div>
                    
                    <a class="yelp-logo" href="${url}" target="_blank"></a>
                </div>
            </div>
        </div>
    `,

    yelpSimpleCard: (data) => `
        <div class="yelp-simple-card">
            ${mapTemplates.yelpMarkerPopup(data)}
        </div>
    `,

    yelpCard: ({
        name,
        image_url,
        categories,
        url,
    }) => `
        <div class="yelp-card">
            <a class="yelp-card__img-wrap mb10 ${!image_url ? 'default-img-bg-small' : ''}" href="${url}" target="_blank">
                ${image_url ? `<img src=${image_url} alt="#" class="of"/>` : ''}
            </a>
            
            <a class="yelp-card__title link-dark h6 mb5" href="${url}" target="_blank">${name}</a>
            <span class="yelp-card__categories mb5 tiny-text">${categories.map(item => item.title).join(', ')}</span>
            <a class="yelp-logo" href="${url}" target="_blank"></a>
        </div>
    `,
}