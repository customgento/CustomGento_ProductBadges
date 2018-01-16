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
});
