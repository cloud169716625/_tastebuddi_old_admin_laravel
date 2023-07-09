<template>
  <div class="content" style="padding: 0 20px;">
    <el-card shadow="none">
       <div slot="header">
        <span>Verified Items</span>
        <el-button
              size="small"
              type="primary"
              class="float-right"
              @click="dialogFormVisible = true"
              >ADD</el-button
            >
      </div>
      <el-table :data="tableData" stripe border style="width: 100%">
        <el-table-column prop="item_name" label="Item Name"> </el-table-column>
        <el-table-column prop="category[0].category_name" label="Category">
        </el-table-column>
        <el-table-column label="Price">
          <template slot-scope="scope">
            {{ formatPrice(scope.row.price) }}
          </template>
        </el-table-column>
        <el-table-column label="">
          <template slot-scope="scope">
            <el-button
              type="danger"
              class="float-right"
              size="mini"
              @click="
                untaggedItem(tableData[scope.$index].id, scope.$index)
              "
              >Delete</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </el-card>
    <TagItemForm
      :cityId="cityId"
      :locationId="locationId"
      :state="dialogFormVisible"
      @dialogState="dialogState"
    />
  </div>
</template>

<script>
import TagItemForm from "./Forms/TagItem.vue";
export default {
  props: ['locationId', 'cityId'],
  data() {
    return {
      tableData: [],
      dialogFormVisible: false,
    };
  },
  components: {
    TagItemForm
  },
  watch: {
    async locationId(value) {
      const response = await axios.get(
        `/ajax/verified-businesses/${value}/tagged-items`
      );
      const data = response.data.data;
      this.tableData = data;
    },
  },
  methods: {
    async untaggedItem(id, index) {
      this.$confirm('This will untagged the item from the location, continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }).then(() => {
        axios.delete(
          `/ajax/verified-businesses/${this.locationId}/untagged-item/${id}`
        ).then(_ => {
          this.tableData.splice(index, 1);

          this.$notify.success({
            title: "Success",
            message: "Item deleted successfully",
          });
        }).catch(_ => {
          this.$notify.error({
            title: 'Failed',
            message: 'Something went wrong.'
          })
        });
      });
    },

    dialogState(e) {
      this.dialogFormVisible = e;
    },

    formatPrice(value) {
      let val = (value/1).toFixed(2)
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }
  },
};
</script>
