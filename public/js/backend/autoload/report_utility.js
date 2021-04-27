$("#collapse").on("click", toggleResult);

var options = {
  format: "DD/MM/YYYY",
  startDate: moment().startOf("month"),
  endDate: moment().endOf("month"),
  dateLimit: {
    days: 31,
  },
  showDropdowns: true,
  showWeekNumbers: true,
  timePicker: false,
  timePickerIncrement: 1,
  timePicker12Hour: true,
  ranges: {
    "Hôm nay": [moment(), moment()],
    "Hôm qua": [moment().subtract(1, "days"), moment().subtract(1, "days")],
    "7 ngày trước": [moment().subtract(6, "days"), moment()],
    "30 ngày trước": [moment().subtract(29, "days"), moment()],
    "Tuần này": [moment().startOf("isoWeek"), moment().endOf("isoWeek")],
    "Tuần trước": [
      moment().subtract(7, "days").startOf("isoWeek"),
      moment().subtract(7, "days").endOf("isoWeek"),
    ],
    "Tháng này": [moment().startOf("month"), moment().endOf("month")],
    "Tháng trước": [
      moment().subtract(1, "month").startOf("month"),
      moment().subtract(1, "month").endOf("month"),
    ],
  },
  opens: "left",
  drops: "up",
  buttonClasses: ["btn", "btn-sm"],
  applyClass: "btn-success",
  cancelClass: "btn-secondary",
  separator: " to ",
  locale: {
    applyLabel: "Chọn",
    cancelLabel: "Hủy",
    fromLabel: "Từ",
    toLabel: "đến",
    customRangeLabel: "Tùy chọn",
    daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
    monthNames: [
      "Tháng 1",
      "Tháng 2",
      "Tháng 3",
      "Tháng 4",
      "Tháng 5 ",
      "Tháng 6",
      "Tháng 7",
      "Tháng 8",
      "Tháng 9",
      "Tháng 10",
      "Tháng 11",
      "Tháng 12",
    ],
    firstDay: 1,
  },
};
$(document).on("click", ".accordion", function (e) {
  var tr = $(this).parent();
  tr.toggleClass("active-select");
  var dataIndex = tr.attr("data-index");
  var children = tr.siblings(".child[data-index=" + dataIndex + "]");
  if (children.css("display") == "table-row") {
    children.css("display", "none");
  } else {
    children.css("display", "table-row");
  }
});

function toggleResult() {
  let $filter = $(".filter");
  let $result = $(".result");

  let $clickToggleRightDiv = $("#collapse");
  if ($filter.is(":visible")) {
    $filter.hide();
    $result.css("width", "100%");
  } else {
    $filter.show();
    $result.css("width", "100%").css("width", "-=420px");
  }
}

$("#reportrange span").html(
  moment().startOf("month").locale("vi").format("D MMMM, YYYY") +
    " - " +
    moment().endOf("month").locale("vi").format("D MMMM, YYYY")
);

$("#reportrange").daterangepicker(options, function (start, end, label) {
  $("#reportrange span").html(
    start.locale("vi").format("D MMMM, YYYY") +
      " - " +
      end.locale("vi").format("D MMMM, YYYY")
  );
});

let Tool = {
  downloading: false,
  url: null,
  button: null,
  processBar: null,
  serverData: [],

  init: function () {
    this.button = $(".export-report");
    const self = this;

    self.button.on("click", function (e) {
      e.preventDefault();

      var data = me.createRequestData();

      sendRequest(
        {
          url: reportUri,
          type: "POST",
          data: data.data,
        },
        function (response) {
          if (!response) return;
          var d =
            typeof me.beforeGenerateFile !== "undefined"
              ? me.beforeGenerateFile(data)
              : "";
          var results = me.generateReportTable(response.data);
          if (results.length != 0) {
            var config = {
              object: d,
              data: data.clientData,
              summary: response.summary,
            };
            self.generateFile(results, config);
          }
        }
      );
    });
  },
  header: function () {
    return [["id"], ["reg_no"]];
  },
  reduce: function (items) {
    let finalItems = [];
    items.map(function (item) {
      finalItems.push([
        [item.id, "Number"],
        [item.reg_no, "String"],
      ]);
    });
    return finalItems;
  },
  toObject: function (arr) {
    let rv = {};
    for (let i = 0; i < arr.length; ++i) rv[i] = arr[i];
    return rv;
  },
  updateProgress: function (e, t) {
    let n = Math.floor((e / t) * 100);
    this.processBar.text(n + "%");
    this.processBar.css("width", n + "%");
  },
  generateProcessBar: function () {
    let processBarContainer = this.button
      .closest(".modal-content")
      .find(".progress");
    this.processBar = processBarContainer.find(".progress-bar");
    processBarContainer.removeClass("invisible");
  },
  loadData: function (e) {
    if (this.downloading) {
      let self = this;
      self.generateFile();
    }
  },
  generateFile: function (results, config) {
    let e = this,
      zip = new JSZip(),
      xl = zip.folder("xl");
    xl.file("workbook.xml", Workbook.xml());
    xl.file("_rels/workbook.xml.rels", Workbook.xmlRels());
    zip.file("_rels/.rels", Workbook.rels());
    zip.file("[Content_Types].xml", Workbook.contentType());
    xl.file(
      "worksheets/sheet1.xml",
      Worksheet.generateXMLWorksheet(results, config)
    );
    zip.generateAsync({ type: "blob" }).then(function (t) {
      saveAs(t, "BaoCao_" + moment().format("YYYY_MM_DD_HH_mm_ss") + ".xlsx");
    });
  },
};
Tool.init();
let Workbook = {
  xml: function () {
    return (
      '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\n' +
      '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" ' +
      'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' +
      'xmlns:mx="http://schemas.microsoft.com/office/mac/excel/2008/main" ' +
      'xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" ' +
      'xmlns:mv="urn:schemas-microsoft-com:mac:vml" ' +
      'xmlns:x14="http://schemas.microsoft.com/office/spreadsheetml/2009/9/main" ' +
      'xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac" ' +
      'xmlns:xm="http://schemas.microsoft.com/office/excel/2006/main">' +
      "<workbookPr/>" +
      '<sheets><sheet state="visible" name="Sheet1" sheetId="1" r:id="rId3"/></sheets><definedNames/><calcPr/></workbook>'
    );
  },
  xmlRels: function () {
    return (
      '<?xml version="1.0" ?>\n' +
      '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">\n' +
      '<Relationship Id="rId3" Target="worksheets/sheet1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/>\n</Relationships>'
    );
  },
  rels: function () {
    return (
      '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\n' +
      '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' +
      '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' +
      "</Relationships>"
    );
  },
  contentType: function () {
    return (
      '<?xml version="1.0" ?>\n' +
      '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">\n' +
      '<Default ContentType="application/xml" Extension="xml"/>\n' +
      '<Default ContentType="application/vnd.openxmlformats-package.relationships+xml" Extension="rels"/>\n' +
      '<Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" PartName="/xl/worksheets/sheet1.xml"/>\n' +
      '<Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" PartName="/xl/workbook.xml"/>\n' +
      "</Types>"
    );
  },
};
let Worksheet = {
  sanitizeStringForXML: function (e) {
    "use strict";
    let t = /[^\x09\x0A\x0D\x20-\xFF\x85\xA0-\uD7FF\uE000-\uFDCF\uFDE0-\uFFFD]/gm;
    return e.replace(t, "");
  },
  xml: function () {
    return (
      '<?xml version="1.0" ?>' +
      '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" ' +
      'xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" ' +
      'xmlns:mv="urn:schemas-microsoft-com:mac:vml" ' +
      'xmlns:mx="http://schemas.microsoft.com/office/mac/excel/2008/main" ' +
      'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' +
      'xmlns:x14="http://schemas.microsoft.com/office/spreadsheetml/2009/9/main" ' +
      'xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac" ' +
      'xmlns:xm="http://schemas.microsoft.com/office/excel/2006/main">' +
      "<sheetData>{placeholder}</sheetData></worksheet>"
    );
  },
  generateXMLWorksheet: function (results, config) {
    let n = "";
    n += this.generateHeader(results, config);
    n += this.generateRows(results, config);
    return this.xml().replace("{placeholder}", n);
  },
  generateHeader: function (results, config) {
    var n = this;
    return me.generateHeader(results, config, n);
  },
  generateRows: function (results, config) {
    var n = this;
    return me.generateRows(results, config, n);
  },
  generateCells: function (e) {
    let t = "",
      n = this;
    e.map(function (e) {
      let a = e.length > 1 && "Number" === e[1] ? "Number" : "String";
      switch (a) {
        case "String":
          t += n.generateCellString(e[0]);
          break;
        default:
          t += n.generateCellNumber(e[0]);
      }
    });
    return t;
  },
  generateCellString: function (t) {
    let a = this.htmlEscape(t);
    a = this.sanitizeStringForXML(a);
    return '<c t="inlineStr"><is><t>' + a + "</t></is></c>";
  },
  generateCellNumber: function (e) {
    return "<c><v>" + e + "</v></c>";
  },
  htmlEscape: function (e) {
    e += "";
    e = e
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&apos;");
    return e;
  },

  formatNumberExcel(number, decimals, decPoint, thousandsSep) {
    if (Number.isNaN(number)) {
      return "0";
    }
    decimals = Math.abs(decimals) || 4;
    number = parseFloat(number);

    if (!decPoint || !thousandsSep) {
      decPoint = ".";
      thousandsSep = ",";
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
};
