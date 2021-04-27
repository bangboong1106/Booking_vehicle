import Lang from "@/common/Lang";
import moment from "moment/moment";

export default {
  methods: {
    generateRules(entity, fields) {
      let rules = {};
      for (let idx in fields) {
        let field = fields[idx];
        rules[field.name] = [];
        if (typeof field.validator != "undefined") {
          rules[field.name].push({
            validator: field.validator,
            trigger: field.trigger || "blur",
          });
        } else {
          if (typeof field.required != "undefined") {
            rules[field.name].push({
              required: true,
              message: "Vui lòng nhập " + Lang[entity].attribute[field.name],
              trigger: field.trigger || "blur",
            });
          }
          if (typeof field.min != "undefined") {
            rules[field.name].push({
              min: "Độ dài " + Lang[field.name] + " tối thiểu là " + field.min,
              max: "Độ dài " + Lang[field.name] + " tối đa là " + field.max,
              trigger: field.trigger || "blur",
            });
          }
        }
      }
      return rules;
    },
    generateFormData(config) {
      let data = {};
      for (let prop in config) {
        let element = config[prop];
        data[prop] = "";
        switch (element.dataType) {
          case "array":
            data[prop] = [];
            break;
          case "number":
            data[prop] = 0;
            if (typeof element.defaultValue != "undefined") {
              data[prop] = element.defaultValue;
            }
            break;
          case "date":
            data[prop] = null;
            if (element.defaultValue === "now") {
              let increment = element.increment || 0;
              data[prop] = moment().add(increment, "days");
            }
            break;
          case "time":
            data[prop] = null;
            if (element.defaultValue === "now") {
              data[prop] = moment();
            }
            break;
        }
        if (element.relation) {
          data[element.relation] = void 0;
        }
      }

      return data;
    },
  },
};
