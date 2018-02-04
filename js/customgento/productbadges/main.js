document.observe('dom:loaded', function(){
    (function (d) {
        var badgePositions = d.getElementsByClassName("product-badges-chooser-position-box-allowed");

        var renderContainerPositionInput = d.getElementById("render_container_position_input");

        for (var i = 0; i < badgePositions.length; i++) {
            var positionValue = badgePositions[i].getAttribute("data-position");

            // Mark the active position box
            if (positionValue === renderContainerPositionInput.value) {
                badgePositions[i].classList.add("product-badges-chooser-position-box-active");
            }

            badgePositions[i].addEventListener(
                "click",
                function () {
                    //Remove active class from all boxes
                    var badgeActivePositions = d.getElementsByClassName("product-badges-chooser-position-box-allowed");

                    for (var i = 0; i < badgeActivePositions.length; i++) {
                        badgeActivePositions[i].classList.remove("product-badges-chooser-position-box-active");
                    }

                    // Add active class
                    this.classList.add("product-badges-chooser-position-box-active");

                    // Set the value to hidden input
                    renderContainerPositionInput.value = this.getAttribute("data-position");
                },
                false
            )
        }
    }(document));

    (function (d) {
        var badgeStoreEnablers = d.getElementsByClassName("badges_store_enabler");

        for (var i = 0; i < badgeStoreEnablers.length; i++) {

            badgeStoreEnablers[i].addEventListener(
                "click",
                function () {
                    var controlledChooserId = this.getAttribute("data-controlled-chooser");
                    var controlledDefaultId = this.getAttribute("data-controlled-default");

                    var checked = this.checked;

                    if (checked) {
                        d.getElementById(controlledChooserId).disabled = true;
                        d.getElementById(controlledDefaultId).disabled = false;
                    } else {
                        d.getElementById(controlledChooserId).disabled = false;
                        d.getElementById(controlledDefaultId).disabled = true;
                    }
                },
                false
            )
        }
    }(document));

    (function (d) {
        var visualisationInputs = d.getElementsByClassName("trigger-badge-preview");
        var badgePreviewHolder = d.getElementById('badge_preview_holder_field');

        var formKey = badgePreviewHolder.getAttribute('data-form-key');
        var badgePreviewUrl = badgePreviewHolder.getAttribute('data-update-url');
        var ajaxLoaderImageUrl = badgePreviewHolder.getAttribute('data-loader-image');

        var updatePreviewArea = function() {
            badgePreviewHolder.innerHTML = this.response;
        };

        var visualisationChanged = function() {
            // We don't have preview for the image
            if ('image' == this.value) {
                return;
            }

            badgePreviewHolder.innerHTML = '<img src="' + ajaxLoaderImageUrl + '"></img>';

            var badgePreviewXHR = new XMLHttpRequest();

            badgePreviewXHR.addEventListener('load', updatePreviewArea);

            badgePreviewXHR.open('POST', badgePreviewUrl);
            badgePreviewXHR.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            badgePreviewXHR.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var data = {
                form_key: formKey
            };

            var visualisationInputs = d.getElementsByClassName("trigger-badge-preview");

            for (var i = 0; i < visualisationInputs.length; i++) {
                data[visualisationInputs[i].getAttribute('name')] = visualisationInputs[i].value;
            }

            // Preparing POST params
            var params = typeof data == 'string' ? data : Object.keys(data).map(
                function(k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
            ).join('&');

            badgePreviewXHR.send(params);
        };

        for (var i = 0; i < visualisationInputs.length; i++) {
            visualisationInputs[i].addEventListener('change', visualisationChanged);
        }

        visualisationChanged();
    }(document));
});
