/*
Template Name: Velzon - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: Ecommerce Dashboard init js
*/

// get colors array from the string
function getChartColorsArray(chartId) {
    if (document.getElementById(chartId) !== null) {
        var colors = document.getElementById(chartId).getAttribute("data-colors");
        if (colors) {
            colors = JSON.parse(colors);
            return colors.map(function (value) {
                var newValue = value.replace(" ", "");
                if (newValue.indexOf(",") === -1) {
                    var color = getComputedStyle(document.documentElement).getPropertyValue(
                        newValue
                    );
                    if (color) return color;
                    else return newValue;
                } else {
                    var val = value.split(",");
                    if (val.length == 2) {
                        var rgbaColor = getComputedStyle(
                            document.documentElement
                        ).getPropertyValue(val[0]);
                        rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                        return rgbaColor;
                    } else {
                        return newValue;
                    }
                }
            });
        } else {
            console.warn('data-colors atributes not found on', chartId);
        }
    }
}

// Simple Donut Charts

// var chartDonutBasicColors = getChartColorsArray("store-visits-source");
// if (chartDonutBasicColors) {
//     var options = {
//         series: [
//             44,
//             55,
//             // 41,
//             // 17,
//             // 15
//         ],
//         labels: [
//             "Nhà ăn tầng 3",
//             "Nhà ăn B1",
//             // "Email",
//             // "Other",
//             // "Referrals"
//         ],
//         chart: {
//             height: 333,
//             type: "donut",
//         },
//         legend: {
//             position: "bottom",
//         },
//         stroke: {
//             show: false
//         },
//         dataLabels: {
//             dropShadow: {
//                 enabled: false,
//             },
//         },
//         colors: chartDonutBasicColors,
//     };

//     var chart = new ApexCharts(
//         document.querySelector("#store-visits-source"),
//         options
//     );
//     chart.render();
// }

// world map with markers
var vectorMapWorldMarkersColors = getChartColorsArray("sales-by-locations");
if (vectorMapWorldMarkersColors) {
    var worldemapmarkers = new jsVectorMap({
        map: "world_merc",
        selector: "#sales-by-locations",
        zoomOnScroll: false,
        zoomButtons: false,
        selectedMarkers: [0, 5],
        regionStyle: {
            initial: {
                stroke: "#9599ad",
                strokeWidth: 0.25,
                fill: vectorMapWorldMarkersColors[0],
                fillOpacity: 1,
            },
        },
        markersSelectable: true,
        markers: [{
            name: "Palestine",
            coords: [31.9474, 35.2272],
        },
        {
            name: "Russia",
            coords: [61.524, 105.3188],
        },
        {
            name: "Canada",
            coords: [56.1304, -106.3468],
        },
        {
            name: "Greenland",
            coords: [71.7069, -42.6043],
        },
        ],
        markerStyle: {
            initial: {
                fill: vectorMapWorldMarkersColors[1],
            },
            selected: {
                fill: vectorMapWorldMarkersColors[2],
            },
        },
        labels: {
            markers: {
                render: function (marker) {
                    return marker.name;
                },
            },
        },
    });
}

// Vertical Swiper
var swiper = new Swiper(".vertical-swiper", {
    slidesPerView: 2,
    spaceBetween: 10,
    mousewheel: true,
    loop: true,
    direction: "vertical",
    autoplay: {
        delay: 2500,
        disableOnInteraction: false,
    },
});

var layoutRightSideBtn = document.querySelector('.layout-rightside-btn');
if (layoutRightSideBtn) {
    document.querySelectorAll(".layout-rightside-btn").forEach(function (item) {
        var userProfileSidebar = document.querySelector(".layout-rightside-col");
        item.addEventListener("click", function () {
            if (userProfileSidebar.classList.contains("d-block")) {
                userProfileSidebar.classList.remove("d-block");
                userProfileSidebar.classList.add("d-none");
            } else {
                userProfileSidebar.classList.remove("d-none");
                userProfileSidebar.classList.add("d-block");
            }
        });
    });
    window.addEventListener("resize", function () {
        var userProfileSidebar = document.querySelector(".layout-rightside-col");
        if (userProfileSidebar) {
            document.querySelectorAll(".layout-rightside-btn").forEach(function () {
                if (window.outerWidth < 1699 || window.outerWidth > 3440) {
                    userProfileSidebar.classList.remove("d-block");
                } else if (window.outerWidth > 1699) {
                    userProfileSidebar.classList.add("d-block");
                }
            });
        }
    });
    var overlay = document.querySelector('.overlay');
    if (overlay) {
        document.querySelector(".overlay").addEventListener("click", function () {
            if (document.querySelector(".layout-rightside-col").classList.contains('d-block') == true) {
                document.querySelector(".layout-rightside-col").classList.remove("d-block");
            }
        });
    }
}

window.addEventListener("load", function () {
    var userProfileSidebar = document.querySelector(".layout-rightside-col");
    if (userProfileSidebar) {
        document.querySelectorAll(".layout-rightside-btn").forEach(function () {
            if (window.outerWidth < 1699 || window.outerWidth > 3440) {
                userProfileSidebar.classList.remove("d-block");
            } else if (window.outerWidth > 1699) {
                userProfileSidebar.classList.add("d-block");
            }
        });
    }
});