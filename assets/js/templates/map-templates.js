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
        display_phone,
        phone,
        image_url,
        displayAddress,
        name,
        rating,
        review_count,
        url,
    }) => `
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