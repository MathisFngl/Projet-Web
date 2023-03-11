function calculateRSI(prices, period) {
  let rsiValues = [];
  let gainSum = 0;
  let lossSum = 0;
  for (let i = 0; i < prices.length; i++) {
    if (i >= period) {
      let gain = 0;
      let loss = 0;
      for (let j = i - period + 1; j <= i; j++) {
        let priceDiff = prices[j] - prices[j - 1];
        if (priceDiff > 0) {
          gain += priceDiff;
        } else {
          loss += -priceDiff;
        }
      }
      gainSum = gainSum - gainSum / period + gain;
      lossSum = lossSum - lossSum / period + loss;
      let rs = gainSum / period / (lossSum / period + gainSum / period);
      let rsi = 100 - 100 / (1 + rs);
      rsiValues.push(rsi);
    } else {
      rsiValues.push(null);
    }
  }
  return rsiValues;
}