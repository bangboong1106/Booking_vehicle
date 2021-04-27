let map;
let gmapMarker = [];
function initMap() {
  let lat =
      typeof $.cookie("journey-lat") !== "undefined"
        ? parseFloat($.cookie("journey-lat"))
        : 17,
    lng =
      typeof $.cookie("journey-lng") !== "undefined"
        ? parseFloat($.cookie("journey-lng"))
        : 108,
    zoom =
      typeof $.cookie("journey-zoom") !== "undefined"
        ? parseInt($.cookie("journey-zoom"))
        : 6,
    menuDisplayed = false,
    menuBox;
  map = new google.maps.Map(document.getElementById("journey_map"), {
    center: new google.maps.LatLng(lat, lng),
    zoom: zoom,
  });

  let infoWindow = new google.maps.InfoWindow();

  let iconBase = baseUrl + "/css/backend/images/",
    icons = {
      truck: {
        icon: {
          url: iconBase + "truck.png",
          scaledSize: new google.maps.Size(27, 35),
        },
        icon_hover: {
          url: iconBase + "truck_hover.png",
          scaledSize: new google.maps.Size(27, 35),
        },
      },
    },
    features = [];

  for (let i = 0; i < vehicles.length; i++) {
    features.push({
      position: new google.maps.LatLng(
        vehicles[i].latitude,
        vehicles[i].longitude
      ),
      type: "truck",
      title: vehicles[i].title,
      plate: vehicles[i].plate,
      label: {
        text: vehicles[i].title,
        color: "#222222",
        fontSize: "12px",
      },
      id: vehicles[i].id,
      weight: vehicles[i].weight,
      volume: vehicles[i].volume,
      length: vehicles[i].length,
      width: vehicles[i].width,
      height: vehicles[i].height,
      status: vehicles[i].status,
      current_location: vehicles[i].current_location,
    });
  }

  for (let i = 0; i < features.length; i++) {
    let marker = new MarkerWithLabel({
      map: map,
      animation: google.maps.Animation.DROP,
      position: features[i].position,
      icon: icons[features[i].type].icon,
      labelContent: features[i].title,
      labelAnchor: new google.maps.Point(50, 12),
      labelClass: "vehicle-plate",
      labelInBackground: true,
      plate: features[i].plate,
      active: false,
    });
    google.maps.event.addListener(
      marker,
      "mouseover",
      (function (marker, i) {
        return function () {
          sendRequestNotLoading(
            {
              url: detailUrl.replace(-1, features[i].id),
              type: "GET",
            },
            function (response) {
              if (response.ok) {
                infoWindow.setContent(response.data.content);
                infoWindow.open(map, marker);
                marker.setIcon(icons[features[i].type].icon_hover);
              } else {
                console.error(response);
              }
            }
          );
        };
      })(marker, i)
    );

    google.maps.event.addListener(marker, "mouseout", function () {
      if (marker.active === false) marker.setIcon(icons[features[i].type].icon);
      infoWindow.close();
    });

    gmapMarker.push(marker);
  }
  map.addListener("rightclick", function (e) {
    for (let i in e) {
      if (e.hasOwnProperty(i) && e[i] instanceof MouseEvent) {
        let mouseEvent = e[i],
          left = mouseEvent.clientX,
          top = mouseEvent.clientY;

        menuBox = document.getElementById("menu");
        menuBox.style.left = left + "px";
        menuBox.style.top = top + "px";
        menuBox.style.display = "block";

        mouseEvent.preventDefault();

        menuDisplayed = true;
      }
    }
  });
  map.addListener("click", function (e) {
    if (menuDisplayed === true) {
      menuBox.style.display = "none";
    }
  });

  let vehicleItem = document.getElementsByClassName("journey-vehicle-item");
  for (let i = 0; i < vehicleItem.length; i++) {
    (function (index) {
      vehicleItem[index].addEventListener("mouseover", function () {
        let plate = this.dataset.plate;
        for (i = 0; i < gmapMarker.length; i++) {
          let marker = gmapMarker[i];
          // If is same category or category not picked
          if (marker.plate === plate) {
            marker.setIcon(icons[features[i].type].icon_hover);
          } else if (!marker.active) {
            marker.setIcon(icons[features[i].type].icon);
          }
        }
      });
      vehicleItem[index].addEventListener("mouseout", function () {
        let plate = this.dataset.plate;
        for (i = 0; i < gmapMarker.length; i++) {
          let marker = gmapMarker[i];
          if (marker.active === true) continue;
          // If is same category or category not picked
          if (marker.plate === plate) {
            marker.setIcon(icons[features[i].type].icon);
          }
        }
      });

      vehicleItem[index].addEventListener("click", function () {
        let activeVehicleItem = document.getElementsByClassName(
            "journey-vehicle-item active"
          ),
          plate = this.dataset.plate;
        if (activeVehicleItem.length > 0)
          activeVehicleItem[0].classList.remove("active");
        vehicleItem[index].classList.add("active");
        for (i = 0; i < gmapMarker.length; i++) {
          let marker = gmapMarker[i];
          if (marker.active === true)
            marker.setIcon(icons[features[i].type].icon);
          marker.active = false;
          marker.setZIndex(100);

          if (marker.plate === plate) {
            marker.setIcon(icons[features[i].type].icon_hover);
            marker.setZIndex(150);
            map.setCenter(marker.getPosition());
            map.setZoom(10);
            marker.active = true;
          }
        }
      });
    })(i);
  }
}
google.maps.event.addDomListener(window, "load", initMap);

$(function () {
  let delay = (function () {
      let timer = 0;
      return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
      };
    })(),
    input = $("#filter-vehicle");

  input.on("keyup", function (e) {
    if (
      (e.keyCode >= 48 && e.keyCode <= 57) ||
      (e.keyCode >= 65 && e.keyCode <= 90) ||
      (e.keyCode >= 96 && e.keyCode <= 105) ||
      e.keyCode === 8 ||
      e.keyCode === 32
    ) {
      delay(function () {
        searchVehicle();
      }, 800);
    }
  });

  $("input[type=checkbox]").change(function () {
    searchVehicle();
  });

  function searchVehicle() {
    showLoading("ul.vehicle-list");
    var statuses = [];
    let partnerId = (userRole == 'admin' && $('#partner-id').length > 0 ? $('#partner-id').val() : userPartnerId);
    $("input[type=checkbox]").each((index, val) => {
      if ($(val).prop("checked")) {
        statuses.push($(val).val());
      }
    });

    let partner = vehicles.filter(function (vehicle) {
      if (partnerId > 0) {
        return vehicle.partner_id == partnerId;
      } else {
        return vehicle;
      }
    });

    let value = optimalText(input.val()),
      filtered = partner.filter(function (vehicle) {
        if (statuses.length == 0) {
          return vehicle.plate.indexOf(value) !== -1;
        } else {
          return (
            vehicle.plate.indexOf(value) !== -1 &&
            statuses.includes(vehicle.status)
          );
        }
      }),
      listPlate = [];
    $("ul.vehicle-list li").hide();
    $.each(filtered, function (index, val) {
      $('ul.vehicle-list li[data-plate="' + val.plate + '"]').show();
      hideLoading("ul.vehicle-list");
      listPlate.push(val.plate);
    });
    if (listPlate.length === 0) {
      hideLoading("ul.vehicle-list");
      $(".not-found").show();
    } else {
      $(".not-found").hide();
    }

    for (var i = 0; i < gmapMarker.length; i++) {
      let marker = gmapMarker[i];
      if (listPlate.includes(marker.plate)) {
        marker.setVisible(true);
      } else {
        marker.setVisible(false);
      }
    }
  }

  function optimalText(value) {
    let tmp = $.trim(value);
    tmp = tmp.replace(new RegExp("/s|-|./", "gm"), "");
    tmp = tmp.toUpperCase();
    return tmp;
  }

  $(document).on("click", ".collapse-bar", function () {
    let filter = $(".col-md-3.filter"),
      map = $(".journey-map");
    if (filter.hasClass("d-none")) {
      map.removeClass("col-md-12").addClass("col-md-9");
    } else {
      map.removeClass("col-md-9").addClass("col-md-12");
    }
    filter.toggleClass("d-none");
  });

  if (typeof urlPartner !== 'undefined' && $('.select-partner').length > 0) {
    cboSelect2.partner(urlPartner);
  }

  $('.select-partner').on('select2:select', function(e){
    searchVehicle();
  });

  $('.select-partner').on('select2:clear', function(e){
    searchVehicle();
  });
});
