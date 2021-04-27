<template>
  <div>
    <label class="row">{{label}}</label>
    <div class="row">
      <div class="col-md-12">
        <input
          :type="inputType"
          :class="{'editing': isEditing}"
          :disabled="!isEditing"
          :placeholder="placeholder"
          v-model="value"
        />
        <span v-if="!isEditing" @click="editTextBox()" class="ti-pencil"></span>
        <span v-if="isEditing" class="ti-check" @click="saveValue()"></span>
        <span v-if="isEditing" class="ti-close" @click="cancelEdit()"></span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    placeholder: {
      type: String,
      default: ""
    },
    inputType: {
      type: String,
      default: "text"
    },
    value: {
      type: String,
      default: ""
    },
    label: {
      type: String,
      default: ""
    },
    fieldName: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      isEditing: false
    };
  },
  methods: {
    editTextBox: function() {
      if (this.inputType === "text") {
        this.isEditing = true;
      } else if (this.inputType === "password") {
        this.$emit("changePassword");
      }
    },
    saveValue: function() {
      this.isEditing = false;
      axios
        .post("c-user/save", {
          params: {
            fieldName: this.fieldName,
            value: this.value
          }
        })
        .then(res => {});
    },
    cancelEdit: function() {
      this.isEditing = false;
    }
  }
};
</script>

<style lang="scss" scoped>
.editing {
  border: 1px solid #1e88e5;
}
label {
  margin: 0;
  color: #000000;
  font-size: 13px;
  font-weight: bold !important;
  margin-top: 10px;
}

input {
  padding: 2px 5px;
  position: relative;
  width: 100%;
  border: none;
  border-bottom: 1px solid #cccccc;
  &:disabled {
    background-color: #ffffff;
  }
  &:hover + span {
    display: inline;
  }
}

span {
  position: absolute;
  top: 50%;
  right: 22px;
  cursor: pointer;
  transform: translate(0, -50%);
  &::before {
    color: blue;
  }
}

span.ti-pencil {
  display: none;
  &:hover {
    display: inline;
  }
}

span.ti-check {
  right: 45px;
}

span.ti-close::before {
  color: #aaaaaa;
}

@media (max-width: 768px) {
  span.ti-pencil {
    display: inline;
  }
}
</style>