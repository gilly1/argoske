<template>
        <div>
            <div  class="col-md-12">
              <label>Name</label>
              <select style="width:40vw" v-model="model_mapping" name="model_mapping" @change="onSelectModelMapping($event)" type="text" class="form-control" >
                <option value="" selected disabled>Select Model ...</option>
                  <option v-for="model in model_mappings" :key="model.id" :value="model.id">{{model.name}}</option>
              </select>
            </div>

          <div style="width:65vw" class="form-group has-feedback col-md-12 row" v-if="approvers.length > 0" v-for="(experience, index) in fields" :key="index">
            <div class="form-group col-md-5">
              <label>Name</label>
              <select v-model="experience.name" :name="`fields[${index}][name]`" type="text" class="form-control" >
                  <option v-for="appr in approvers" :key="appr.id" :value="appr.id">{{appr.name}}</option>
              </select>
            </div>
            <div class="form-group col-md-5">
              <label>Sequence</label>
              <input v-model="experience.counter"  :name="`fields[${index}][counter]`" type="text" class="form-control" placeholder="approval sequence">
            </div>
            
            <div class="form-group col-md-2">
              <div class="row">
                <div class="col-m-2"></div>
                  <div class="col-md-6">
                    <label for="">Action</label>
                    <button @click="addExperience" type="button" class="btn btn-sm btn-success"><i class="fa fa-plus "></i></button>
                    <button @click="removeField(index)" type="button" class="btn btn-sm btn-danger ml-1"><i class="fa fa-trash"></i></button>
                  </div>
              </div>
            </div> 
          </div>
               
        <hr>
        </div>

</template>

<script>

export default {  
  data: () => ({
    model_mappings :[],
    model_mapping : '',
    approvers : [],
    count : 2,
    fields: [
      {
        name: "",
        counter: 1,
      }
    ],
    db_types: [],
  }),
   mounted() {
        this.getModelMappings();
    },

  methods: {
    addExperience () {
      this.fields.push({
        name: '',
        counter: this.count++
      })
    },
    removeField (index) {
      this.count--;
      this.fields.splice(index, 1);
    },
    onSelectModelMapping(event){
      this.fields = [
          {
            name: "",
            counter: 1,
          }
        ];
        this.count = 2;
      this.getModelMappingsApprovers(event.target.value);
    },
    getModelMappings(){
        axios.get('/api/mappings')
            .then((response) => {
                this.model_mappings = response.data;

            })
            .catch( resonse => {
                console.log('error');
            })
    },
    getModelMappingsApprovers(id){
        axios.get('/api/mappingApprovers/' + id)
            .then((response) => {
                this.approvers = response.data;

            })
            .catch( resonse => {
                console.log('error');
            })
    },

    submit () {
      const data = {
        fields: this.fields
      }
      alert(JSON.stringify(data, null, 2))
    }
  }
};
</script>

<style>

</style>
