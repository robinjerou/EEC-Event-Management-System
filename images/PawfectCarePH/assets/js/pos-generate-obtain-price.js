    function getQueryParams() {
        const params = {};
        window.location.search.substring(1).split("&").forEach(pair => {
          const [key, value] = pair.split("=");
          params[decodeURIComponent(key)] = decodeURIComponent(value || '');
        });
        return params;
      }
  
      document.addEventListener("DOMContentLoaded", function() {
        const params = getQueryParams();
        if (params.totalPrice) {
          document.getElementById("total").textContent = parseFloat(params.totalPrice).toFixed(2);
        }
      });