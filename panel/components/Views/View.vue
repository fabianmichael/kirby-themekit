<template>
	<k-panel-inside class="k-themes-view">
		<k-header>
			Themes

			<template #buttons>
				<slot name="buttons" />
			</template>
		</k-header>

		<slot />
	</k-panel-inside>
</template>

<script>
export const props = {
	props: {
		data: [Object, Array],
	},
};

export default {
	mixins: [props],
};
</script>

<style>
.k-themes-view .k-header:has(+ .k-themes-stats) {
	margin-bottom: 0;
}

@container (max-width: 30rem) {
	.k-themes-view .k-header {
		flex-direction: column;
	}
}
</style>
