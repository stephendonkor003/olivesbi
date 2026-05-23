/**
 * Created by degre on 12/21/2017.
 */
var deviceAttributes = DataManager.getDeviceAttributes();
var positionAttributes = Object.keys(devicePosition);

positionAttributes.forEach(function (key) {
    var value = devicePosition[key];
    switch (key) {
        case "attributes":
            var attributes = devicePosition[key];
            var attributeKeys = Object.keys(attributes);
            attributeKeys.forEach(function (akey) {
                value = attributes[akey];
                switch (akey) {
                    case "distance":
                        value = FormatFactory.formatDistanceValue(value, "km");
                        break;
                    case "totalDistance":
                        value = FormatFactory.formatDistanceValue(value, "km");
                        break;
                }
                var aname = deviceAttributes.attributes[akey];
                if (!aname) {
                    aname = akey;
                }
                table += '<tr><td>' + aname + '</td><td>' + value + '</td></tr>';

            });
            break;
        case "course":
            value = FormatFactory.courseFormatter(value);
            break;
        case "latitude":
        case "longitude":
            value = FormatFactory.formatCordinate(key, value, "");
            break;
        case "speed":
            value = FormatFactory.formatSpeedValue(value, "kmh", "");
            break;
        default:

    }
    var aname = deviceAttributes[key];
    if (!aname) {
        aname = key;
    }
    table += '<tr><td>' + aname + '</td><td>' + value + '</td></tr>';


});
table += "</table>";
if (marker._popup != undefined) {
    marker.unbindPopup();
}
marker.closePopup();
marker.bindPopup(table);
marker.openPopup();
//return table;