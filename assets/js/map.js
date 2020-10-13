    window.initMap = () => {
        let ajaxPath = $('#map').data('path');
        let infoWindow = new google.maps.InfoWindow;
        let map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 50.8599882, lng: -90.9302304 },
            zoom: 4
        });
        let northEast = [];
        let southWest = [];
        map.addListener("idle", () => {
            northEast['lat'] = map.getBounds().getNorthEast().lat();
            northEast['lng'] = map.getBounds().getNorthEast().lng();
            southWest['lat'] = map.getBounds().getSouthWest().lat();
            southWest['lng'] = map.getBounds().getSouthWest().lng();
            console.log(northEast);
            console.log(southWest);
        });
        $.ajax(
            {
                url: ajaxPath,
                dataType: "json",
                data: northEast,southWest,
                method: "POST",
                success: function (data) {
                    const markers = data.map((markerElem) => {
                        let infowincontent = document.createElement('div');
                        let strong = document.createElement('strong');
                        strong.textContent = markerElem.mlsNum
                        infowincontent.appendChild(strong);
                        infowincontent.appendChild(document.createElement('br'));
                        let text = document.createElement('text');
                        text.textContent = markerElem.address
                        infowincontent.appendChild(text);

                        let marker = new google.maps.Marker({
                            position:  new google.maps.LatLng(
                                parseFloat(markerElem.lat),
                                parseFloat(markerElem.lng)),
                        });
                        marker.addListener('click', function () {
                            infoWindow.setContent(infowincontent);
                            infoWindow.open(map, marker);
                        });
                        return marker;
                    });
                    new MarkerClusterer(map, markers, {
                        imagePath:
                            "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m",
                    });
                },
            }
        );
    }