<template>
        <div>
          <div class="form-group has-feedback col-md-12 row" v-for="(experience, index) in fields" :key="index">
            <div class="form-group col-md-2">
              <label>Field Name</label>
              <input v-model="experience.field_name" :name="`fields[${index}][field_name]`" type="text" class="form-control" placeholder="Input Field">
            </div>
            <div class="form-group col-md-2">
              <label>DB column</label>
              <input v-model="experience.db_column" :name="`fields[${index}][db_column]`" type="text" class="form-control" placeholder="Database Column">
            </div>
            <div class="form-group col-md-2">
              <label>Type</label>
              <select v-model="experience.type" :name="`fields[${index}][type]`" type="text" class="form-control" >
                  <option v-for="(db_type,index) in db_types" :key="index" :value="db_type.value">{{db_type.type}}</option>
              </select>
            </div>
            <div class="form-group col-md-2">
              <label>Size</label>
              <input v-model="experience.size" :name="`fields[${index}][size]`" type="text" class="form-control" placeholder="Size">
            </div>
            <div class="form-group col-md-2">
              <label>Validation</label>          
              <select v-model="experience.validation" :name="`fields[${index}][validation]`" type="text" class="form-control" >
                  <option value="" selected>None</option>
                  <option v-for="(validation,index) in validation" :key="index" :value="validation.value">{{validation.type}}</option>
              </select>
            </div>
            
            <div class="form-group col-md-2">
              <div class="row">
                <label for="">Action</label>
                <div class="col-sm-1"></div>
                  <div class="col-sm-8">
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
    fields: [
      {
        type: "integer",
        field_name: "",
        db_column: "",
        size: "",
        validation: ""
      }
    ],
    db_types: [
        {
          value:"integer",
          type:"Integer",
        },
        {
          value:"longText",
          type:"Long Text",
        },
        {
          value:"string",
          type:"String",
        },
        {
          value:"text",
          type:"Text",
        },
        {
          value:"unsignedBigInteger",
          type:"Unsigned Big Integer",
        },
        {
          value:"year",
          type:"Year",
        },
        {
          value:"dateTime",
          type:"Date Time",
        },
        {
          value:"boolean",
          type:"Boolean",
        },
        {
          value:"enum",
          type:"Enum",
        },
        {
          value:"double",
          type:"Double",
        },
        {
          value:"float",
          type:"Float",
        },
    ],
     validation: [
        {
          value:"nullable",
          type:"Nullable",
        },
        {
          value:"unique",
          type:"unique",
        },
        {
          value:"foreign",
          type:"Foreign",
        }
    ]
  }),

  methods: {
    addExperience () {
      this.fields.push({
        type: '',
        field_name: '',
        db_column: '',
        size: '',
        validation: ''
      })
    },
    removeField (index,) {
      this.fields.splice(index, 1);
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
