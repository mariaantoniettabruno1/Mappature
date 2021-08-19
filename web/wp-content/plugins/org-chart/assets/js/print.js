jQuery(document).ready(function () {
  var canvas = document.querySelector("canvas");
  var el = document.getElementById("print_chart");
  if (el) {
    document
      .querySelector("a#print_chart")
      .addEventListener("click", function (e) {
        jQuery("a.rmv-nd.close").hide();
        e.preventDefault();
        // console.log(jQuery("#chart table").html());
        // jQuery("#chart").append(jQuery("#chart table").html());

        html2canvas(document.querySelector("#chart > .jOrgChart > table"), {
          canvas: canvas
        }).then(function (canvas) {
          /* console.log('Drew on the existing canvas');*/
          var $canvas = jQuery("canvas"); // canvas var stored at the top of the code

          /* console.log("show"); */
          var dataURL = $canvas[0].toDataURL("image/png");

          canvas.toBlobHD(function (blob) {
            saveAs(blob, "chart.png");
          }, "image/png");
        });
        setTimeout(function () {
          jQuery("a.rmv-nd.close").show();
        }, 3000);
        false;
      });
  }
  if (jQuery("a").hasClass("print_chart_front")) {
    jQuery(document).on("click", "a.print_chart_front", function (e) {
      var chartid = jQuery(this).data("chartid");
      e.preventDefault();
      // console.log(jQuery("#chart table").html());
      // jQuery("#chart").append(jQuery("#chart table").html());

      html2canvas(document.querySelector("#chart_" + chartid + " > .jOrgChart > table"), {
        canvas: canvas
      }).then(function (canvas) {
        /* console.log('Drew on the existing canvas');*/
        var $canvas = jQuery("canvas"); // canvas var stored at the top of the code

        /* console.log("show"); */
        var dataURL = $canvas[0].toDataURL("image/png");

        canvas.toBlobHD(function (blob) {
          saveAs(blob, "chart_" + chartid + ".png");
        }, "image/png");
      });

      return false;
    });
  }
});