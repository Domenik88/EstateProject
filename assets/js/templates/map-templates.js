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
        categoriesTitles,
        url,
    }) => `
        <div class="yelp-marker-popup-inner">
            <div class="yelp-marker-popup-inner__img-wrap ${!image_url && 'default-img-bg-small'}">
                ${image_url && `<img src=${image_url} alt="#" class="of"/>`}
            </div>
            
            <div class="yelp-marker-popup-inner__info">
                <div class="yelp-marker-popup-inner__description">
                    <span class="yelp-marker-popup-inner__title h6">${name}</span>
                    <span class="yelp-marker-popup-inner__categories tiny-text">${categoriesTitles}</span>
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

    yelpCard: ({
        name,
        image_url,
        categoriesTitles,
        url,
    }) => `
        <div class="yelp-card">
            <div class="yelp-card__img-wrap mb10 ${!image_url && 'default-img-bg-small'}">
                ${image_url && `<img src=${image_url} alt="#" class="of"/>`}
            </div>
            
            <span class="yelp-card__title h6 mb5">${name}</span>
            <span class="yelp-card__categories mb5 tiny-text">${categoriesTitles}</span>
            <a class="yelp-logo" href="${url}" target="_blank"></a>
        </div>
    `,
}