// Characterization tests for assets/js/weighing-calc.js. Run: node --test "tests/js/*.test.js"
// The legacy* helpers are the formulas as they were written inline in index.php before the refactor.
const test = require('node:test');
const assert = require('node:assert/strict');
const calc = require('../../assets/js/weighing-calc.js');

// Indicator parsing as written inline in the old setInterval handler
function legacyIndicator(ind, data) {
    let shown = null;
    if (ind == 'X2S' || ind == 'X722') {
        if (data.includes("GS")) {
            const text = data.split(" ");
            shown = text[text.length - 1].replace("kg", "").replace("KG", "").replace("Kg", "");
        }
    } else if (ind == 'BDI') {
        if (data.includes("GS") || data.includes("NT") || data.includes("ST") || data.includes("US")) {
            const text = data.split(" ");
            shown = text[text.length - 1].replace("kg", "").replace("KG", "").replace("Kg", "");
        }
    } else if (ind == 'EX2001') {
        data = data.replace("kg", "").replace("KG", "").replace("Kg", "").replace("g", "");
        if (data != null && data != '') {
            const text = data.split(",");
            shown = parseInt(text[text.length - 1].replaceAll(",", "").trim()).toString();
        }
    } else if (ind == 'D2008') {
        if (data.includes("GS")) {
            const text = data.split(",");
            shown = parseInt(text[text.length - 1].replace("kg", "").replace("KG", "").replace("Kg", "")).toString();
        }
    }
    return shown;
}

function legacyPrices(unitPrice, currentWeightText) {
    const price = unitPrice ? parseFloat(unitPrice).toFixed(2) : 0.00;
    const weight = currentWeightText ? parseFloat(currentWeightText) : 0;
    const subTotalPrice = price * weight;
    const sstPrice = subTotalPrice * 0.08;
    return { price, subTotal: subTotalPrice, sst: sstPrice, total: subTotalPrice + sstPrice };
}

test('num matches the old `val ? parseFloat(val) : 0` coercion', () => {
    assert.equal(calc.num(''), 0);
    assert.equal(calc.num(undefined), 0);
    assert.equal(calc.num(null), 0);
    assert.equal(calc.num('0'), 0);
    assert.equal(calc.num('12.5'), 12.5);
    assert.equal(calc.num('1,200'), 1);
    assert.ok(Number.isNaN(calc.num('abc')));
});

test('nett is the absolute difference', () => {
    assert.equal(calc.nett(15000, 5000), 10000);
    assert.equal(calc.nett(5000, 15000), 10000);
    assert.equal(calc.nett(0, 0), 0);
    assert.equal(calc.nett(10.5, 0).toFixed(0), '11');
});

test('Different Container second weighing', () => {
    // gross2 30000 with a 9000 kg second vehicle leaves a 21000 kg empty container
    assert.equal(calc.emptyContainer2(30000, 9000), 21000);
    assert.equal(calc.emptyContainer2(9000, 30000), 21000);
    // tare2 40000 - vehicle 9000 - first nett 12000
    assert.equal(calc.differentContainerNett(40000, 9000, 12000), 19000);
    // negative intermediate is made positive
    assert.equal(calc.differentContainerNett(5000, 9000, 12000), 16000);
});

test('currentWeight per weight type', () => {
    assert.equal(calc.currentWeight('Normal', 12000, 0), 12000);
    assert.equal(calc.currentWeight('Container', 12000, 30000), 18000);
    assert.equal(calc.currentWeight('Empty Container', 30000, 12000), 18000);
    assert.equal(calc.currentWeight('Different Container', 12000, 19000), 19000);
});

test('finalWeight takes the reduce weight off, made positive', () => {
    assert.equal(calc.finalWeight(10000, 250), 9750);
    assert.equal(calc.finalWeight(100, 250), 150);
    assert.equal(calc.finalWeight(100, 0), 100);
});

test('prices match the old inline calculation', () => {
    const cases = [['', '1000'], ['0', '1000'], ['12.345', '1000'], ['2.5', ''], ['2.5', '0'], ['3.999', '1234']];
    for (const [unitPrice, weight] of cases) {
        assert.deepEqual(calc.prices(unitPrice, weight), legacyPrices(unitPrice, weight), `${unitPrice} x ${weight}`);
    }

    const p = calc.prices('12.345', '1000');
    assert.equal(p.price, '12.35');
    assert.equal(p.subTotal.toFixed(2), '12350.00');
    assert.equal(p.sst.toFixed(2), '988.00');
    assert.equal(p.total.toFixed(2), '13338.00');
});

test('indicator readings match the old parser for every model', () => {
    const cases = [
        ['X2S', 'ST,GS,   12340kg'],
        ['X2S', 'ST GS 12340kg'],
        ['X2S', 'ST NT 12340kg'],
        ['X722', 'GS 800KG'],
        ['BDI', 'NT 450Kg'],
        ['BDI', 'US 10kg'],
        ['BDI', 'OL ----'],
        ['EX2001', '+ 1,234 kg'],
        ['EX2001', 'ww,12340kg'],
        ['EX2001', 'kg'],
        ['D2008', 'ST,GS,+0012340kg'],
        ['D2008', 'ST,NT,+0012340kg'],
        ['UNKNOWN', 'GS 100kg'],
    ];
    for (const [model, data] of cases) {
        assert.equal(calc.parseIndicatorReading(model, data), legacyIndicator(model, data), `${model}: ${data}`);
    }

    assert.equal(calc.parseIndicatorReading('X2S', 'ST GS 12340kg'), '12340');
    assert.equal(calc.parseIndicatorReading('X2S', 'ST NT 12340kg'), null);
    assert.equal(calc.parseIndicatorReading('D2008', 'ST,GS,+0012340kg'), '12340');
    assert.equal(calc.parseIndicatorReading('EX2001', 'kg'), null);
    assert.equal(calc.parseIndicatorReading('UNKNOWN', 'GS 100kg'), null);
});

test('batchLabel maps the status filter codes', () => {
    assert.equal(calc.batchLabel('N'), 'Pending');
    assert.equal(calc.batchLabel('Y'), 'Complete');
    assert.equal(calc.batchLabel('C'), 'Cancelled');
    assert.equal(calc.batchLabel(''), '');
    assert.equal(calc.batchLabel('-'), '-');
});
