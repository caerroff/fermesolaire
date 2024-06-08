const RADIUS = 6378.137 * 1000;


async function getCodeInsee(latitude, longitude) {
    const response = await fetch('https://geo.api.gouv.fr/communes/?lat=' + latitude + '&lon=' + longitude)
    const data = await response.json()
    const codeInsee = data[0].code
    return codeInsee
}

function rad(_) {
    return _ * Math.PI / 180;
}

function area(coords) {
    var p1, p2, p3, lowerIndex, middleIndex, upperIndex, i,
        area = 0,
        coordsLength = coords.length;

    if (coordsLength > 2) {
        for (i = 0; i < coordsLength; i++) {
            if (i === coordsLength - 2) {// i = N-2
                lowerIndex = coordsLength - 2;
                middleIndex = coordsLength - 1;
                upperIndex = 0;
            } else if (i === coordsLength - 1) {// i = N-1
                lowerIndex = coordsLength - 1;
                middleIndex = 0;
                upperIndex = 1;
            } else { // i = 0 to N-3
                lowerIndex = i;
                middleIndex = i + 1;
                upperIndex = i + 2;
            }
            p1 = coords[lowerIndex];
            p2 = coords[middleIndex];
            p3 = coords[upperIndex];
            area += (rad(p3[0]) - rad(p1[0])) * Math.sin(rad(p2[1]));
        }

        area = area * RADIUS * RADIUS / 2;
    }

    return Math.abs(area);
}

export { getCodeInsee, area };