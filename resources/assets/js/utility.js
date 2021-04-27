export default {
  methods: {
    formatNumber(number) {
      if (Number.isNaN(number)) {
        return "0";
      }
      let decimals = 4,
        decPoint = ",",
        thousandsSep = ".";
      decimals = Math.abs(decimals) || 4;
      number = parseFloat(number);

      if (!decPoint || !thousandsSep) {
        decPoint = ",";
        thousandsSep = ".";
      }

      var roundedNumber = Math.round(Math.abs(number) * ("1e" + decimals)) + "";
      var numbersString = decimals
        ? roundedNumber.slice(0, decimals * -1) || 0
        : roundedNumber;
      var decimalsString = decimals ? roundedNumber.slice(decimals * -1) : "";
      var formattedNumber = "";

      while (numbersString.length > 3) {
        formattedNumber =
          thousandsSep + numbersString.slice(-3) + formattedNumber;
        numbersString = numbersString.slice(0, -3);
      }

      if (decimals && decimalsString.length === 1) {
        while (decimalsString.length < decimals) {
          decimalsString = decimalsString + decimalsString;
        }
      }
      return (
        (number < 0 ? "-" : "") +
        numbersString +
        formattedNumber +
        (decimalsString && decimalsString != "0000"
          ? decPoint + decimalsString.replace(/0+$/, "")
          : "")
      );
    },
  },
};
