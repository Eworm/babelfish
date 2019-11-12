<template>
<publish-fields :fields="fields" :data.sync="data" :regular-title-field="true"></publish-fields>
</template>


<style>
.babelfish-fieldtype>.field-inner>label {
    display: none !important;
}

.babelfish-fieldtype,
.babelfish-fieldtype .publish-fields {
    padding: 0 !important;
}
</style>


<script>
export default {

    mixins: [Fieldtype],

    computed: {

        fields() {
            return _.chain(this.config.fields).map((field, name) => {
                field.name = name;
                return field;
            }).values().value();
        }

    }

}
</script>
