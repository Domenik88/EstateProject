export const mapTemplates = {
    markerPopup: ({
        img,
        listingPrice,
        address,
        metrics,
        mls,
    }) => `
        <div class="marker-popup-inner">
            <div class="marker-popup-inner__left-col">
                <div class="marker-popup-inner__img-wrap mb5">
                    ${img 
                        ? `<img src=${img} alt="#" class="marker-popup-inner__img of"/>` 
                        : `<div class="marker-popup-inner__img of default-img-bg"></div>`
                    }
                </div>
                
                ${listingPrice ?
                    `<span class="tiny-text_bold">${_formatCurrencyCa(listingPrice)}</span>`
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
                
                ${mls ?
                    `<span class="gray-mls-after icon-mls-min tiny-text">${mls}</span>`
                : ''}
            </div>
        </div>
    `,

    estateCard: ({
         images,
         isNew,
         forSaleByOwner,
         favoritePath,
         userFavorite,
         loginHref,
         favoriteJsMod,
         listingPrice,
         address,
         metrics,
         mlsNumber,
    }) => `
        <div class="estate-card js-estate-card">
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
                        ${isNew ? `<span class="estate-card__label schild">NEW</span>` : ''}
                        ${forSaleByOwner ? `<span class="estate-card__label schild">for sale by owner</span>` : ''}
                    </div>
        
                    <a
                        class="circle-button _ic-fs-12 favorite-toggle ${userFavorite ? '_active' : '' } ${favoriteJsMod ? favoriteJsMod : ''}"
                        data-url="${favoritePath}"
                        href="${loginHref}"
                    ></a>
                </div>
                
                <div class="estate-card__controls-wrap js-slider-nav">
                    <span class="circle-button _bordered icon-angle-left js-arrow-left"></span>
                    <span class="circle-button _bordered icon-angle-right js-arrow-right"></span>
                </div>
            </div>
            
            <div class="estate-card__description pt30 pb30">
                ${listingPrice ? 
                    `<span class="estate-card__title subtitle mb10">${_formatCurrencyCa(listingPrice)}</span>`
                : ''}
              
                ${(address && address.streetAddress && address.city) ? 
                    `<a class="estate-card__location h5 mb10 link-dark" href="#">${address.streetAddress + ', ' + address.city}</a>` 
                : ''}
                
                ${metrics ? `
                    <div class="estate-card__metrics-wrap mb20">
                        ${mapTemplates.metrics(metrics)}
                    </div>
                ` : ''}
                
                ${mlsNumber ? 
                    `<span class="gray-mls-after icon-mls-min tiny-text">${'MLS® ' + mlsNumber}</span>`
                : ''}
            </div>
        </div>
    `,

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
                        ${prefix ? `<span class="metrics__label small-text2">${prefix}</span>` : ''}
                        <span class="metrics__val small-text">${val}</span>
                        ${suffix ? `<span class="metrics__label small-text2">${suffix}</span>` : ''}
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