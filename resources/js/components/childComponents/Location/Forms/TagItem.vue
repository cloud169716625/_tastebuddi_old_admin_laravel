<template>
  <div>
    <el-dialog
      class="tag-item"
      title="Add Item"
      :visible.sync="dialogFormVisible"
    >
      <el-form :model="data" :rules="rules" ref="data">
        <label>Item name</label>
        <el-form-item prop="item_id">
          <el-autocomplete
            v-model="autoCompleteState"
            :fetch-suggestions="getItems"
            placeholder="Search an item"
            @select="handleSelect"
            :debounce="500"
          >
          </el-autocomplete>
        </el-form-item>
        <div class="text-center" v-if="itemNotFound">
          <span>Item not found!</span><br />
          <el-button type="primary" plain size="mini" @click="createItem"
            >Create instead</el-button
          >
        </div>
        <label>Price</label>
        <el-form-item prop="price">
          <el-input class="price" placeholder="" v-model="data.price">
            <template slot="prepend">$</template>
          </el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">Cancel</el-button>
        <el-button type="primary" @click="addItem" :loading="saveLoading" :disabled="saveDisabled">Confirm</el-button>
      </span>
    </el-dialog>

    <CreateItem
      :locationId="locationId"
      :state="createDialogFormVisible"
      @dialogState="dialogState"
      :itemName="autoCompleteState"
    />
  </div>
</template>
<style lang="scss">
.tag-item {
  .el-autocomplete {
    width: 100%;
  }
  .el-dialog {
    width: 25%;
  }
  .price {
    width: 80%;
  }
}
</style>
<script>
import CreateItem from "./CreateItem.vue";

export default {
  components: {
    CreateItem,
  },
  props: ["locationId", "state", "cityId"],
  data: () => ({
    data: {
      price: "",
      item_id: ""
    },
    dialogFormVisible: false,
    createDialogFormVisible: false,
    formLabelWidth: "120px",
    items: [],
    autoCompleteState: "",
    timeout: null,
    itemNotFound: false,
    rules: {
      item_id: [
        {
          required: true,
          message: "Please select an item.",
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
    saveLoading: false,
    saveDisabled: false
  }),
  watch: {
    state(value) {
      this.dialogFormVisible = value;
    },
    dialogFormVisible(value) {
      this.$emit("dialogState", value);
    },
  },
  methods: {
    async getItems(queryString, cb) {
      const response = await axios.get(
        `/ajax/cities/${this.cityId}/items?search=${queryString}`
      );
      this.items = response.data.data;
      if (this.items.length !== 0) {
        this.items.map((item) => {
          item.value = item.item_name;
          return item;
        });
      }
      this.toggleItemNotFound();
      cb(this.items);
    },
    toggleItemNotFound() {
      if (this.autoCompleteState != "" && this.items.length === 0) {
        this.itemNotFound = true;
      } else {
        this.itemNotFound = false;
      }
    },
    handleSelect(item) {
      this.data.item_id = item.item_id;
    },
    addItem() {
      this.$refs["data"].validate(async (valid) => {
        if (valid) {
          this.saveLoading = true

          axios.post(
            `/ajax/item/details`,
            { ...this.data, 'location_id' : this.locationId}
          ).then(_ => {
            this.$notify.success({
              title: "Success",
              message: "Item Detail added successfully",
            });

            this.saveDisabled = true

            setTimeout(() => {
              location.reload();
            }, 1500);
          }).catch(_ => {
            this.$notify.error({
              title: "Error",
              message: "Something went wrong.",
            });
          }).finally(_ => {
            this.saveLoading = false
          });
        }
      });
    },
    dialogState(e) {
      this.createDialogFormVisible = e;
    },
    createItem() {
      this.dialogFormVisible = false;
      this.createDialogFormVisible = true;
    },
  },
};
</script>
