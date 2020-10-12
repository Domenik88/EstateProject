    window.initMap = () => {
        let map = new google.maps.Map(document.getElementById('map'), {
            center: new google.maps.LatLng(50.8599882, -90.9302304),
            zoom: 4
        });
        let ajaxPath = $('#map').data('path');
        $.ajax(
            {
                url: ajaxPath,
                dataType: "json",
                method: "POST",
                success: function (data) {
                    points(data);
                },
            }
        );

        let infoWindow = new google.maps.InfoWindow;
        // Change this depending on the name of your PHP or XML file
        function points(data) {
            Array.prototype.forEach.call(data, function (markerElem) {
                var name = markerElem.mlsNum;
                var address = markerElem.mlsNum;
                var point = new google.maps.LatLng(
                    parseFloat(markerElem.lat),
                    parseFloat(markerElem.lng));
                var infowincontent = document.createElement('div');
                var strong = document.createElement('strong');
                strong.textContent = name
                infowincontent.appendChild(strong);
                infowincontent.appendChild(document.createElement('br'));

                var text = document.createElement('text');
                text.textContent = address
                infowincontent.appendChild(text);
                var marker = new google.maps.Marker({
                    map: map,
                    position: point,
                });
                marker.addListener('click', function () {
                    infoWindow.setContent(infowincontent);
                    infoWindow.open(map, marker);
                });
            });
        }
    }