<template>
	<div class="k-theme-editable-field-preview">
		<span v-if="accessibility">{{ accessibility }}</span>
    <k-icon v-else type="cancel" color="red" />
	</div>
</template>

<script>
export default {
	props: {
		value: Boolean,
		column: Object,
	},
  computed: {
    accessibility() {
      if (!this.value.length) {
        return false;
      }

      return this.value[this.value.length - 1]
    }
  }
};
</script>

<style>

.k-theme-editable-field-preview {
	display: flex;
	justify-content: center;
  font-weight: var(--font-semi);
}

.k-theme-editable-field-preview .k-icon {
	transform: scale(1.1);
}

</style>
