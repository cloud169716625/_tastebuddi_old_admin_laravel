<template>
    <div>
        <h1 style="text-align: center; margin-bottom: 50px;">TravelBuddi Terms and Conditions</h1>
        <div v-html="terms"></div>
    </div>
</template>
<script>
export default {
    name: 'Terms',
    data() {
        return {
            terms: null
        }
    },
    mounted() {
        this.loadTermsOfServices()
    },
    methods: {
        loadTermsOfServices() {
            axios.get(`api/v1/settings/pages/terms_of_service`)
                .then(res => {
                    const data = res.data

                    this.terms = data.value
                })
        }
    }
}
</script>