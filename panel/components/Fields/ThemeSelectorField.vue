<template>
  <k-field
    v-bind="$props"
    class="theme-selector-field"
  >
    <k-input
      :id="_uid"
      ref="input"
      v-bind="$props"
      type="theme-selector"
      v-on="$listeners"
      @reset="onInput('')"
    />
  </k-field>
</template>

<script>

export default {
  props: {
    label: String,
    themes: Object,
    value: String,
    help: String,
    disabled: Boolean,
    required: Boolean,
    none: Boolean,
    custom: Boolean,
    previewColors: Array,
  },

  methods: {
    focus() {
      this.$refs.input.focus();
    },
    onInput(value) {
      this.$emit("input", value);
    },
  },
}
</script>

<style lang="css">

.theme-selector-field .k-input[data-invalid] {
  box-shadow: none;
}

.k-input[data-type="theme-selector"] {
  background: transparent;
  border: none;
  outline: 0 !important;
}

</style>
