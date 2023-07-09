<template>
    <div id="item-list-wrapper">

        <div class="page-header">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">

                    <a href="#">
                        Home
                    </a>

                </li>

                <li class="breadcrumb-item active">

                    <a href="/admin/settings/">
                        Settings
                    </a>

                </li>

            </ol>

        </div>

        <div class="content p-20 mt-5">
            <loading-screen ref="loadingScreen">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <h3 style="display: inline-block;">Terms of Service</h3>
                        <el-button style="float: right;" size="medium" type="primary" icon="el-icon-edit" @click="saveTermsOfService" :loading="termsOfServiceLoading">Save</el-button>
                    </div>
                <ElementTiptap v-model="form.termsOfService" :extensions="extensions" lang="en"></ElementTiptap>
                </el-card>

                <el-card class="box-card mt-5">
                    <div slot="header" class="clearfix">
                        <h3 style="display: inline-block;">Privacy Policy</h3>
                        <el-button style="float: right;" size="medium" type="primary" icon="el-icon-edit" @click="savePrivacyPolicy" :loading="privacyPolicyLoading">Save</el-button>
                    </div>
                <ElementTiptap v-model="form.privacyPolicy" :extensions="extensions" lang="en"></ElementTiptap>
                </el-card>
            </loading-screen>
        </div>
    </div>
</template>

<script>
import LoadingScreen from 'vue-loading-screen';
import 'element-tiptap/lib/index.css';
import {
    ElementTiptap,
    Doc,
    Text,
    Paragraph,
    Heading,
    Bold,
    Italic,
    Strike,
    Underline,
    Link,
    // Image,
    Blockquote,
    ListItem,
    BulletList, // use with ListItem
    OrderedList, // use with ListItem
    TodoItem,
    TodoList, // use with TodoItem
    TextAlign,
    Indent,
    // HorizontalRule,
    HardBreak,
    History,
    // LineHeight,
    // Iframe,
    // CodeBlock,
    // TrailingNode,
    // Table, // use with TableHeader, TableCell, TableRow
    // TableHeader,
    // TableCell,
    // TableRow,
    // FormatClear,
    // TextColor,
    // TextHighlight,
    // Preview,
    // Print,
    Fullscreen,
    // CodeView
    // SelectAll,
} from 'element-tiptap';

export default{
    name: 'settings-index',
    components: {
        LoadingScreen,
        ElementTiptap
    },
    data() {
        return {
            form: {
                termsOfService: null,
                privacyPolicy: null,
            },
            extensions: [
                new Doc(),
                new Text(),
                new Paragraph(),
                new Heading({ level: 5 }),
                new Bold({ bubble: true }),
                new Underline({ bubble: true }),
                new Italic({ bubble: true }),
                new Strike({ bubble: true }),
                new Link({ bubble: true }),
                new Blockquote(),
                new TextAlign(),
                new ListItem(),
                new BulletList({ bubble: true }),
                new OrderedList({ bubble: true }),
                new TodoItem(),
                new TodoList(),
                new Indent(),
                new HardBreak(),
                new Fullscreen(),
                new History()
            ],
            types: {
                termsOfService: 'terms_of_service',
                privacyPolicy: 'privacy'
            },
            termsOfServiceLoading: false,
            privacyPolicyLoading: false
        }
    },
    methods: {
        async saveTermsOfService() {
            this.termsOfServiceLoading = true

            const data = {
                name: this.types.termsOfService,
                value: this.form['termsOfService']
            }

            axios.put('/ajax/settings', data)
                .then(_ => {
                    this.$notify.success({
                        title: 'Success',
                        message: 'Terms of Service is updated.',
                    });
                })
                .catch(_ => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                })
                .finally(_ => {
                    this.termsOfServiceLoading = false
                })
        },

        async savePrivacyPolicy() {
            this.privacyPolicyLoading = true

            const data = {
                name: this.types.privacyPolicy,
                value: this.form['privacyPolicy']
            }

            axios.put('/ajax/settings', data)
                .then(_ => {
                    this.$notify.success({
                        title: 'Success',
                        message: 'Privacy Policy is updated.',
                    });
                })
                .catch(_ => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong',
                    });
                })
                .finally(_ => {
                    this.privacyPolicyLoading = false
                })
        }
    },
    async mounted() {
        axios.get(`/ajax/settings/${this.types.termsOfService}`)
            .then(res => {
                const data = res.data

                this.form.termsOfService = data.value
            })

        axios.get(`/ajax/settings/${this.types.privacyPolicy}`)
            .then(res => {
                const data = res.data

                this.form.privacyPolicy = data.value
            })
    }
}
</script>
