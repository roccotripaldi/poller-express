Chart.defaults.global.responsive = true;

var ctx = document.getElementById("poex_results").getContext("2d");
var data = {
    labels: poex_result_lables,
    datasets: [
        {
            label: "Poll Results",
            fillColor: "rgba(242,103,34,.8)",
            strokeColor: "rgba(255,255,255,1)",
            pointColor: "rgba(242,103,34,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(242,103,34,1)",
            data: poex_result_totals

        }
    ]
};
console.log( poex_result_lables );
var poex_results = new Chart(ctx).Line(data);