<template>
  <div class="content p-20">
    <el-card :body-style="{ padding: '0px' }" shadow="none">
      <div slot="header">
        <el-row>
          <el-col :span="12"> Add Item </el-col>
          <el-col :span="12">
            <el-button
              type="primary"
              class="float-right"
              @click="dialogFormVisible = true"
              >ADD</el-button
            >
          </el-col>
        </el-row>
      </div>
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
  props: ["locationId", "cityId"],
  components: {
    TagItemForm,
  },
  data: () => ({
    dialogFormVisible: false,
  }),
  methods: {
    dialogState(e) {
      this.dialogFormVisible = e;
    },
  },
};
</script>
