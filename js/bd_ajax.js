jQuery(document).ready(function ($) {
  $("#bd_ajax_form input[type='checkbox']").change(function () {
    var phonescript = $("#phonescript").prop("checked") ? 1 : 0;
    var seometa = $("#seometa").prop("checked") ? 1 : 0;
    var linkalt = $("#linkalt").prop("checked") ? 1 : 0;
    var imglinkalt = $("#imglinkalt").prop("checked") ? 1 : 0;
    var set_external_links = $("#set_external_links").prop("checked") ? 1 : 0;
    var set_internal_links = $("#set_internal_links").prop("checked") ? 1 : 0;
    var bool = false;
    if ($(this).is(":checked")) {
      bool = true;
    }
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      dataType: "json",
      data: {
        action: "bd_ajax_action",
        phonescript: phonescript,
        seometa: seometa,
        linkalt: linkalt,
        imglinkalt: imglinkalt,
        set_external_links: set_external_links,
        set_internal_links: set_internal_links,
        bool: bool,
      },
      beforeSend: function () {},
      success: function (response) {},
      complete: function (response) {
        var response = response.responseJSON;
        if (response.success === 1) {
          setTimeout(function () {
            if (response.bool === 1) {
              explodePage();              
            }
            toastr.success("", response.msg);
          }, 350);
        } else {
          setTimeout(function () {
            toastr.error("", response.msg);
          }, 350);
        }
      },
    });

    return false;
  });
});

function explodePage() {
  function random(max) {
    return Math.random() * (max - 0) + 0;
  }

  var particleContainer = document.createDocumentFragment();

  for (var i = 0; i < 500; i++) {
    var styles =
      "top: " +
      random(window.innerHeight) +
      "px; left: " +
      random(window.innerWidth) +
      "px; animation-delay: " +
      random(1000) +
      "ms;";

    var particle = document.createElement("div");
    particle.className = "particle";
    particle.style.cssText = styles.toString();
    particleContainer.appendChild(particle);
  }

  document.body.appendChild(particleContainer);
}

// Call explodePage function after the page has loaded
