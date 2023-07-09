<template>
    <div>
        <h1 style="text-align: center; margin-bottom: 50px;">TravelBuddi Privacy Policy</h1>
        <div v-html="privacy"></div>
    </div>
</template>
<script>
export default {
    name: 'Privacy',
    data() {
        return {
            privacy: null
        }
    },
    mounted() {
        this.loadPrivacyPolicy()
    },
    methods: {
        loadPrivacyPolicy() {
            axios.get(`api/v1/settings/pages/privacy`)
                .then(res => {
                    const data = res.data

                    this.privacy = data.value
                })
        }
    }
}
</script>