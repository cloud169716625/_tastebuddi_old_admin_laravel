<template>
  <el-dialog title="Create Item" :visible.sync="dialogFormVisible">
    <el-form :model="data" :rules="rules" ref="data">
      <el-row>
        <el-col :span="12">
          <label>Image thumbnail</label>
          <el-form-item prop="thumb_nail">
            <ImageUpload @getFile="uploadFile" />
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <label>Item name</label>
          <el-form-item prop="item_name">
            <el-input placeholder="" v-model="data.item_name"></el-input>
          </el-form-item>
          <label>Category</label>
          <el-form-item prop="category_id">
            <el-select v-model="data.category_id">
              <el-option
                v-for="category in categories"
                :key="category.category_id"
                :label="category.category_name"
                :value="category.category_id"
              >
              </el-option>
            </el-select>
          </el-form-item>
          <label>Price</label>
          <el-form-item prop="price">
            <el-input class="price" placeholder="" v-model="data.price">
              <template slot="prepend">$</template>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <span slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible = false">Cancel</el-button>
      <el-button type="primary" @click="createItem" :disabled="saveDisabled" :loading="saveLoading">Confirm</el-button>
    </span>
  </el-dialog>
</template>
<script>
import ImageUpload from "./../../ImageUpload.vue";
export default {
  components: {
    ImageUpload,
  },
  props: ["locationId", "state", 'itemName'],
  data: () => ({
    rules: {
      thumb_nail: [
        {
          required: true,
          message: "The thumbnail field is required.",
          trigger: "change",
        },
      ],
      item_name: [
        {
          required: true,
          message: "The item field is required.",
          trigger: "blur",
        },
      ],
      category_id: [
        {
          required: true,
          message: "The category field is required.",
          trigger: "change",
        },
      ],
      price: [
        {
          required: true,
          message: "The item price is required.",
          trigger: "blur",
        },
        {
          pattern: /^\d+(\.\d{1,2})?$/,
          message: "The item must be numeric.",
          trigger: "change",
        },
      ],
    },
    data: {
      thumb_nail: null,
      item_name: null,
      category_id: null,
      price: null,
    },
    dialogFormVisible: false,
    imageUrl: "",
    categories: [],
    saveLoading: false,
    saveDisabled: false
  }),
  watch: {
    state(value) {
      this.dialogFormVisible = value;

      if (value) {
        this.data.item_name = this.itemName
      }
    },
    dialogFormVisible(value) {
      this.$emit("dialogState", value);
    },
  },
  async mounted() {
    const response = await axios.get("/ajax/item/categories");
    const data = response.data.data;
    this.categories = data.categories;
  },
  methods: {
    uploadFile(file) {
      this.data.thumb_nail = file;
    },
    createItem() {
      this.$refs["data"].validate(async (valid) => {
        if (valid) {
          this.saveLoading = true

          const formData = this.formData();

          axios.post(
            `/ajax/verified-businesses/${this.locationId}/create-item`,
            formData
          ).then(_ => {
            this.$notify.success({
              title: "Success",
              message: "Item added successfully",
            });

            this.saveDisabled = true

            setTimeout(() => {
              location.reload();
            }, 1500);
          }).catch(err => {
            const response = err.response

            if (response.status == 422) {
              const errors = response.data.errors
              const field = Object.keys(errors)[0]

              this.$notify.error({
                title: 'Error',
                message: errors[field][0]
              })
            } else {
              this.$notify.error({
                title: 'Error',
                message: 'Something went wrong.'
              })
            }
          }).finally(_ => {
            this.saveLoading = false
          })
        }
      });
    },
    formData() {
      let formData = new FormData();
      formData.append("thumb_nail", this.data.thumb_nail);
      formData.append("item_name", this.data.item_name);
      formData.append("category_id", this.data.category_id);
      formData.append("price", this.data.price);

      return formData;
    },
  },
};
</script>
