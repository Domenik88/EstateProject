export const mapTemplates = {
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
                ${image_url && `<img src=${image_url} alt="#" class="of"/>`}
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
            <a class="yelp-card__img-wrap mb10 ${!image_url && 'default-img-bg-small'}" href="${url}" target="_blank">
                ${image_url && `<img src=${image_url} alt="#" class="of"/>`}
            </a>
            
            <a class="yelp-card__title link-dark h6 mb5" href="${url}" target="_blank">${name}</a>
            <span class="yelp-card__categories mb5 tiny-text">${categories.map(item => item.title).join(', ')}</span>
            <a class="yelp-logo" href="${url}" target="_blank"></a>
        </div>
    `,
}