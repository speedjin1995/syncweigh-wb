function calculatePCB(monthlyIncome, epfContribution = 0) {
    const chargeableIncome = monthlyIncome - epfContribution;
    let pcb = 0;

    if (chargeableIncome <= 2500) {
        pcb = 0;
    } else if (chargeableIncome <= 4000) {
        pcb = (chargeableIncome - 2500) * 0.01;
    } else if (chargeableIncome <= 6000) {
        pcb = 15 + (chargeableIncome - 4000) * 0.03;
    } else if (chargeableIncome <= 8000) {
        pcb = 75 + (chargeableIncome - 6000) * 0.08;
    } else if (chargeableIncome <= 10000) {
        pcb = 235 + (chargeableIncome - 8000) * 0.13;
    } else if (chargeableIncome <= 15000) {
        pcb = 495 + (chargeableIncome - 10000) * 0.19;
    } else if (chargeableIncome <= 20000) {
        pcb = 1445 + (chargeableIncome - 15000) * 0.25;
    } else if (chargeableIncome <= 35000) {
        pcb = 2695 + (chargeableIncome - 20000) * 0.26;
    } else if (chargeableIncome <= 50000) {
        pcb = 6595 + (chargeableIncome - 35000) * 0.27;
    } else if (chargeableIncome <= 70000) {
        pcb = 10645 + (chargeableIncome - 50000) * 0.28;
    } else if (chargeableIncome <= 100000) {
        pcb = 16245 + (chargeableIncome - 70000) * 0.29;
    } else {
        pcb = 24945 + (chargeableIncome - 100000) * 0.30;
    }

    return Math.round(pcb * 100) / 100;
}
