// ============================================
// PCB (MTD) helpers using LHDN style equation:
// MTD = ( ( (P - M) * R + B ) - (Z + X) ) / (nPlus1)
// - P: annual chargeable income (annualised basis)
// - M/R/B: from your Table-1 JSON row
// - X: accumulated PCB/MTD already deducted YTD
// - Z: accumulated zakat paid (excluding current month)
// - nPlus1: remaining months in the year INCLUDING current month
//
// MVP assumptions in these functions (change when you have more inputs):
// - Only basicSalary is available => P approximated from salary
// - Z = 0, X = 0, nPlus1 = 12
// ============================================

function getNPlus1(dateInput) {
  var d;
  if (dateInput) {
    var parts = dateInput.split('-');
    // Handle DD-MM-YYYY format
    d = parts.length === 3 ? new Date(parts[2], parts[1] - 1, parts[0]) : new Date(dateInput);
  } else {
    d = new Date();
  }
  return 13 - (d.getMonth() + 1);
}

function findResidentRowByP(P, companyResidentTaxRate) {
  var row = null;
  $.each(companyResidentTaxRate || [], function (_, item) {
    var min = parseFloat(item.min);
    var max = (item.max === "" || item.max == null) ? Infinity : parseFloat(item.max);
    if (!isNaN(min) && P >= min && P <= max) {
      row = item;
      return false; // break
    }
  });
  return row;
}

// 1) NON-RESIDENT (simple flat-rate config)
function calculatePCBNonResident(monthlyRemuneration, companyNonResidentTaxRate) {
  var salary = parseFloat(monthlyRemuneration || 0);
  if (isNaN(salary) || salary <= 0) return 0;

  var rate = parseFloat(companyNonResidentTaxRate || 0); // e.g. 30
  var pcb = salary * (rate / 100);

  return parseFloat(pcb.toFixed(2));
}

// 2) RESIDENT - NORMAL REMUNERATION (monthly salary only, no bonus)
function calculatePCBNormal(monthlySalary, companyResidentTaxRate, opts) {
  opts = opts || {};

  var salary = parseFloat(monthlySalary || 0);
  if (isNaN(salary) || salary <= 0) return 0;

  // MVP: annualise basic salary
  var P = (opts.P != null) ? parseFloat(opts.P) : (salary * 12);

  // Optional inputs (default MVP)
  var X = parseFloat(opts.X || 0);          // accumulated PCB already paid (YTD)
  var Z = parseFloat(opts.Z || 0);          // accumulated zakat paid (excluding current month)
  var nPlus1 = parseInt(opts.nPlus1 || 12); // remaining months incl current

  // If below the first bracket your table starts at, tax is 0
  if (!isFinite(P) || P <= 5000) return 0;

  var row = findResidentRowByP(P, companyResidentTaxRate);
  if (!row) return 0;
  var M = parseFloat(row.m || 0);
  var R = parseFloat(row.r || 0) / 100;
  var category = parseInt(1, 10);
  var B = parseFloat((category === 2 ? row.b2 : row.b1) || 0);

  var annualTax = ((P - M) * R) + B;
  var pcb = (annualTax - (Z + X)) / nPlus1;

  if (!isFinite(pcb) || pcb < 0) pcb = 0;

  // NOTE: LHDN has specific rounding/5-sen rules; implement later if needed.
  return parseFloat(pcb.toFixed(2));
}

// 3) RESIDENT - ANNUALISED REMUNERATION (handles additional remuneration like bonus)
// Simple approach:
// - Pnormal = annualised normal remuneration basis
// - Ptotal  = annualised total (normal + additional for the year)
// - Monthly PCB for normal = calculatePCBNormal(... using Pnormal)
// - Additional PCB in bonus month = (Tax(Ptotal) - Tax(Pnormal)) adjusted for YTD/X/Z if you track them
//
// This matches the idea of computing incremental tax due to additional remuneration.
function calculatePCBAnnualizedRemuneration(monthlySalary, additionalRemunerationThisMonth, companyResidentTaxRate, opts) {
  opts = opts || {};

  var salary = parseFloat(monthlySalary || 0);
  var add = parseFloat(additionalRemunerationThisMonth || 0);

  if (!isFinite(salary) || salary <= 0) return 0;
  if (!isFinite(add) || add < 0) add = 0;

  var category = parseInt(opts.category, 10);
  if (!isFinite(category)) category = 1;

  var X = parseFloat(opts.X || 0);                 // PCB already deducted YTD
  var Z = parseFloat(opts.Z || 0);                 // zakat accumulated (excluding current)
  var nPlus1 = parseInt(opts.nPlus1 || 12, 10);    // remaining months incl current

  // IMPORTANT: P should already be "annual chargeable income" (after reliefs), not just salary*12
  var Pnormal = (opts.Pnormal != null) ? parseFloat(opts.Pnormal) : (salary * 12);
  var Ptotal  = (opts.Ptotal  != null) ? parseFloat(opts.Ptotal)  : (Pnormal + add);

  function annualTaxFromP(P) {
    if (!isFinite(P) || P <= 5000) return 0;

    var row = findResidentRowByP(P, companyResidentTaxRate);
    if (!row) return 0;

    var M = parseFloat(row.m || 0);
    var R = parseFloat(row.r || 0) / 100;
    var B = parseFloat((category === 2 ? row.b2 : row.b1) || 0);

    var t = ((P - M) * R) + B;
    return (isFinite(t) && t > 0) ? t : 0;
  }

  var annualTaxNormal = annualTaxFromP(Pnormal);
  var annualTaxTotal  = annualTaxFromP(Ptotal);

  // Normal PCB for current month (spec-like)
  var pcbNormalMonth = (annualTaxNormal - (Z + X)) / nPlus1;
  if (!isFinite(pcbNormalMonth) || pcbNormalMonth < 0) pcbNormalMonth = 0;

  // Total PCB for the year under the normal scenario
  var totalPcbYearNormal = X + (pcbNormalMonth * nPlus1);

  // Additional remuneration PCB = top-up to match annual tax after bonus
  var pcbAdditionalMonth = annualTaxTotal - totalPcbYearNormal;
  if (!isFinite(pcbAdditionalMonth) || pcbAdditionalMonth < 0) pcbAdditionalMonth = 0;

  var pcbThisMonth = pcbNormalMonth + pcbAdditionalMonth;

  return parseFloat(pcbThisMonth.toFixed(2));
}