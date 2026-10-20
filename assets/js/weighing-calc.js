// Pure weighing calculations used by the weighing screen (index.php).
// Loaded as a plain script in the browser (global WeighCalc) and by Node for the unit tests.
var WeighCalc = (function () {
    // Form value to number: empty is 0, anything else goes through parseFloat (so non-numbers stay NaN)
    function num(value) {
        return value ? parseFloat(value) : 0;
    }

    function nett(gross, tare) {
        return Math.abs(gross - tare);
    }

    // Different Container: the second vehicle carries the replacement bin, so its weight is taken off
    function emptyContainer2(gross2, vehicle2) {
        return Math.abs(gross2 - vehicle2);
    }

    function differentContainerNett(tare2, vehicle2, nett1) {
        return Math.abs(tare2 - vehicle2 - nett1);
    }

    function currentWeight(weightType, nett1, nett2) {
        return weightType == 'Different Container' ? nett2 : Math.abs(nett1 - nett2);
    }

    function finalWeight(current, reduce) {
        return Math.abs(current - reduce);
    }

    // Unit price is rounded to 2 decimals before multiplying, and SST is 8%
    function prices(unitPrice, weight) {
        var price = unitPrice ? parseFloat(unitPrice).toFixed(2) : 0.00;
        var subTotal = price * (weight ? parseFloat(weight) : 0);
        var sst = subTotal * 0.08;

        return { price: price, subTotal: subTotal, sst: sst, total: subTotal + sst };
    }

    function stripKg(text) {
        return text.replace("kg", "").replace("KG", "").replace("Kg", "");
    }

    function lastPart(text, separator) {
        var parts = text.split(separator);
        return parts[parts.length - 1];
    }

    // Weight to display from one indicator message, or null when the message carries no reading
    function parseIndicatorReading(model, data) {
        if (model == 'X2S' || model == 'X722') {
            return data.includes("GS") ? stripKg(lastPart(data, " ")) : null;
        }

        if (model == 'BDI') {
            var hasReading = data.includes("GS") || data.includes("NT") || data.includes("ST") || data.includes("US");
            return hasReading ? stripKg(lastPart(data, " ")) : null;
        }

        if (model == 'EX2001') {
            data = stripKg(data).replace("g", "");
            if (data == '') {
                return null;
            }
            return parseInt(lastPart(data, ",").replaceAll(",", "").trim()).toString();
        }

        if (model == 'D2008') {
            return data.includes("GS") ? parseInt(stripKg(lastPart(data, ","))).toString() : null;
        }

        return null;
    }

    // Status filter code to the label the export endpoints expect
    function batchLabel(code) {
        if (code == 'N') {
            return 'Pending';
        }
        if (code == 'Y') {
            return 'Complete';
        }
        if (code == 'C') {
            return 'Cancelled';
        }
        return code;
    }

    return {
        num: num,
        nett: nett,
        emptyContainer2: emptyContainer2,
        differentContainerNett: differentContainerNett,
        currentWeight: currentWeight,
        finalWeight: finalWeight,
        prices: prices,
        parseIndicatorReading: parseIndicatorReading,
        batchLabel: batchLabel
    };
})();

if (typeof module !== 'undefined') {
    module.exports = WeighCalc;
}
