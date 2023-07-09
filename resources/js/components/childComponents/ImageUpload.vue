<template>
  <el-upload
    class="avatar-uploader"
    action="#"
    :auto-upload="false"
    :show-file-list="false"
    :on-change="onChange"
  >
    <img v-if="imageUrl" :src="imageUrl" class="avatar" />
    <i v-else class="el-icon-plus avatar-uploader-icon"></i>
  </el-upload>
</template>
<style>
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  width: 80%;
  height: 80%;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}
.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  line-height: 178px;
  text-align: center;
}
.avatar {
  width: 178px;
  height: 178px;
  display: block;
}
</style>

<script>
export default {
  data() {
    return {
      imageUrl: "",
    };
  },
  methods: {
    onChange(file) {
      if (file.raw && file.raw["type"].split("/")[0] === "image") {
        this.imageUrl = URL.createObjectURL(file.raw);
        this.$emit("getFile", file.raw);
      } else {
        this.$notify.error({
          title: "Error",
          message: "File must be an image.",
        });
      }
    },
  },
};
</script>
