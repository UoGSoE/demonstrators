<template>
    <a
        @mouseenter="active = !active"
        @mouseleave="active = !active"
        :href="this.href"
        class="button is-gla-success is-outlined is-pulled-right"
    >
        <span class="icon is-small">
            <i
                class="fa"
                :class="{
                    'fa-plus-square' : method == 'Add new'
                }"
                aria-hidden="true"></i>
        </span>
        <span v-if="active">{{method}} {{object}}</span>
    </a>
</template>
<script>
module.exports = {
    props: ['href', 'method', 'object'],
    data() {
        return {
            active: false,
        }
    },
}
</script>