<template>
  <el-row>
    <el-col :span="6">
      <el-select v-model="countryState" filterable placeholder="Search Country" clearable :loading="countryLoading">
        <el-option
          v-for="item in countries"
          :key="item.value"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-col>
    <el-col :span="6">
      <el-select v-model="cityState" filterable placeholder="Search Cities" clearable :loading="cityLoading">
        <el-option
          v-for="item in cities"
          :key="item.value"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-col>
    <el-col :span="6">
      <el-select
        v-model="isVerifiedState"
        placeholder="Verified/Unverified Business"
        clearable
      >
        <el-option value="1" label="Verified"></el-option>
        <el-option value="0" label="Unverified"></el-option>
      </el-select>
    </el-col>
    <el-col :span="3">
      <el-button type="primary"  @click="clearFilters"
        >clear filters</el-button
      >
    </el-col>
  </el-row>
</template>
<style scoped>
.el-autocomplete {
  width: 70%;
}
</style>
<script>
export default {
  data() {
    return {
        cityState: "",
        countryState: "",
        isVerifiedState: null,
        filters: {},
        countries: [],
        cities: [],
        countryLoading: false,
        cityLoading: false
    }
  },
  methods: {
    async getCities() {
      this.cityLoading = true

      const params = {
        searchQuery: this.countryState,
        searchFilter: 'country_id',
        pageLimit: 9999,
        currentSort: 'city_name'
      }

      const response = await axios.get(
        `/ajax/cities?${Object.keys(params).map(key => key + '=' + params[key]).join('&')}`
      );
      const cities = response.data.data.cities.data;

      this.cities = cities.map((city) => {
        return {
          value: city.city_id,
          label: city.city_name,
          country_id: city.country_id
        };
      })

      this.cityLoading = false
    },
    handleChange() {
      const data = {
        country_id: this.countryState,
        city_id: this.cityState,
        is_verified: this.isVerifiedState
      }

      const filters = Object.keys(data).reduce((prev, curr) => {
        if (data[curr]) {
            prev[curr] = data[curr]
        }

        return prev
      }, {})

      this.$emit('getFilters', filters)
    },
    async getCountries() {
      this.countryLoading = true

      const params = {
        searchQuery: '',
        searchFilter: 'country_name',
        pageLimit: 999,
        currentSort: 'country_name'
      }

      const response = await axios.get(
        `/ajax/countries?${Object.keys(params).map(key => key + '=' + params[key]).join('&')}`
      );

      const countries = response.data.data.countries.data;

      this.countries = countries.map((country) => {
        return {
          value: country.country_id,
          label: country.country_name
        }
      });

      this.countryLoading = false
    },
    clearFilters() {
      location.reload();
    },
  },
  async created() {
    await this.getCountries()
    await this.getCities()
  },
  watch: {
    countryState: {
        handler() {
            this.getCities()
            this.cityState = ''

            this.handleChange()
        }
    },
    cityState: {
        handler() {
            this.handleChange()
        }
    },
    isVerifiedState: {
      handler() {
        this.handleChange()
      }
    }
  }
};
</script>
