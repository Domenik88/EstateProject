window._formatInputVal = (obj) => {
    const
        { val, dataFormatParams } = obj,
        {  formatCurrency, prefix='', suffix='', min=-Infinity, max=Infinity } = dataFormatParams,
        pureNum = _getPureNumber(val),
        fixVal = Math.min(Math.max(pureNum, min), max),
        finalVal = formatCurrency ? _formatCurrency(fixVal) : fixVal;

    return prefix + finalVal + suffix;
}

window._formatCurrency = (str) => {
    return str.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1,');
}

window._getPureNumber = (str) => {
    return str.toString().replace(/[^0-9.]/g, "");
}

window._addZero = (num) => {
    return `${num < 10 ? '0' : ''}${num}`
}