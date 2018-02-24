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

        var badgePreviewUrl = badgePreviewHolder.getAttribute('data-update-url');
        var ajaxLoaderImageUrl = badgePreviewHolder.getAttribute('data-loader-image');

        var visualisationChanged = function() {
            // We don't have preview for the image
            if ('image' == this.value) {
                return;
            }

            badgePreviewHolder.innerHTML = '<img src="' + ajaxLoaderImageUrl + '"></img>';

            /**
             * In Magento admin we have global JS variable called FORM_KEY,
             * it is usually defined in Admin page/head.phtml
             */

            var data = {
                form_key: FORM_KEY
            };

            var visualisationInputs = d.getElementsByClassName("trigger-badge-preview");

            for (var i = 0; i < visualisationInputs.length; i++) {
                data[visualisationInputs[i].getAttribute('name')] = visualisationInputs[i].value;
            }

            new Ajax.Request(badgePreviewUrl, {
                parameters: data,
                method: 'post',
                onSuccess: function(transport) {
                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        alert(response.message);
                    }
                    if(response.ajaxExpired && response.ajaxRedirect) {
                        setLocation(response.ajaxRedirect);
                        return;
                    }

                    badgePreviewHolder.innerHTML = response.preview;

                }.bind(this)
            });
        };

        for (var i = 0; i < visualisationInputs.length; i++) {
            visualisationInputs[i].addEventListener('change', visualisationChanged);
        }

        // Triggers badge preview of first load
        visualisationChanged();
    }(document));
});
