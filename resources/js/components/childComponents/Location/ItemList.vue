<template>
  <div class="content" style="padding: 0 20px;">
    <el-card shadow="none">
       <div slot="header">
        <span>Item Details</span>
        <el-button
              size="small"
              type="primary"
              class="float-right"
              @click="dialogFormVisible = true"
              >ADD</el-button
            >
      </div>
      <el-table :data="tableData" stripe border style="width: 100%">
        <el-table-column label="Item Name">
          <template slot-scope="scope">
            {{ scope.row.item.item_name }}
          </template>
        </el-table-column>
        <el-table-column label="Price">
          <template slot-scope="scope">
            {{ formatPrice(scope.row.price) }}
          </template>
        </el-table-column>
        <el-table-column label="Type">
          <template slot-scope="scope">
            {{ getItemDetailType(scope.row) }}
          </template>
        </el-table-column>
        <el-table-column label="Status" align="center">
          <template slot-scope="scope">
            <el-tag
              :type="getItemDetailStatus(scope).color"
              effect="dark">
              {{ getItemDetailStatus(scope).label }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="">
          <template slot-scope="scope">
            <el-button
              type="danger"
              class="float-right"
              size="mini"
              @click="
                deleteItemDetail(tableData[scope.$index].item_detail_id, scope.$index)
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
import TagItemForm from './Forms/TagItem.vue';
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
        `/ajax/locations/${value}/item-details`
      );
      const data = response.data.data;
      this.tableData = data;
    },
  },
  methods: {
    async deleteItemDetail(id, index) {
      this.$confirm('Do you really want to delete this detail?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }).then(() => {
        axios.delete(
          `/ajax/item/details`,
          { data: { item_detail_id: id } }
        ).then(_ => {
          this.tableData.splice(index, 1);

          this.$notify.success({
            title: "Success",
            message: "Item detail deleted successfully",
          });

          setTimeout(() => {
            location.reload();
          }, 1500);
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
    },

    getItemDetailType(itemDetail) {
        if (itemDetail.recommendation) {
            return `Recommendation by ${itemDetail.recommendation.user.full_name}`
        }

        if (itemDetail.isVerifiedBusinessDetail) {
            return `System - Verified Business Item`
        }

        return `System`
    },

    getItemDetailStatus({row, rowIndex}) {
        if (! row.recommendation) return { label: 'N/A', color: 'info' }
        if (row.status === 'rejected') return { label: 'Rejected', color: 'warning' }
        if (row.status === 'approved') return { label: 'Approved', color: 'success' }
        if (row.status === 'pending') return { label: 'Pending', color: 'primary' }

        return { label: 'N/A', color: 'info' }
    },
  },
};
</script>
