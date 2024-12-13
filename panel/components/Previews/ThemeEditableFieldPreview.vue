<template>
	<div class="k-theme-editable-field-preview">
		<k-icon v-if="!value" type="lock" :title="column.label" />
		<p v-else>–</p>
	</div>
</template>

<script>
export default {
	props: {
		value: Boolean,
		column: Object,
	},
};
</script>

<style>

.k-theme-editable-field-preview {
	display: flex;
	justify-content: center;
}

.k-theme-editable-field-preview p {
	color: var(--color-gray-400);
}

.k-theme-editable-field-preview .k-icon {
	transform: scale(0.8);
}

</style>
