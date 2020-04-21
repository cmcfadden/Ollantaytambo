<template>
    <div v-if="tour && stop">
        <div class="row mb-2">
            <div class="col">
                <router-link :to="{'name': 'editTour', params: { tourId: tourId }}">{{tour.title }}</router-link>
                >
                {{ stop.stop_content.title[tour.tour_content.languages[0]] }}
            </div>
        </div>
        <div class="row">
            <div class="col">
                <language-text :languages="tour.tour_content.languages" :text.sync="stop.stop_content.title">
                    Stop Title
                </language-text>
                <hr />
                <draggable v-model="stop.stop_content.stages" handle=".handle">
                    <stage v-for="(stage,key) in stop.stop_content.stages" :key='key' :stage="stage"
                        v-on:remove="stop.stop_content.stages.splice(key)">
                        <component :is="stage.type" :stage="stage" :languages="tour.tour_content.languages"
                            :tour="tour">
                        </component>
                    </stage>
                </draggable>





            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <span>
                    <a :href="previewLink" v-if="stop.id" class="btn btn-outline-success" target="_blank">Preview <i class="fas fa-eye"></i></a>
                    <button @click="save" class="btn btn-primary">Save</button>
                    <save-alert :showAlert.sync="showAlert" />
                </span>

                <div class="col-6">
                    <div class="row d-flex justify-content-end">
                    <select class="form-control col-3" v-model="newStageType">
                        <option disabled></option>
                        <option v-for="(stageType, key) in stageTypes" :key="key" :value="key">{{ stageType }}</option>
                    </select>

                        <button class="btn btn-primary" @click="addStage" :disabled="!newStageType"><i class="fas fa-plus"></i> Add a
                            Stage</button>
                    </div>
                </div>

            </div>
        </div>

    </div>

</template>

<script>
    import draggable from 'vuedraggable'

    export default {
        props: ["stopId", "tourId"],
        components: {
            draggable
        },
        data() {
            return {
                localStop: this.stopId,
                showAlert: false,
                newStageType: null,
                stageTypes: {
                    "separator": "Separator",
                    "ar": "AR",
                    "embed-frame": "Embed",
                    "guide": "Guide",
                    "gallery": "Gallery",
                    "navigation": "Navigation"
                },
                tour: null,
                stop: {
                    stop_content: {
                        "title": {
                             "placeholder": null
                        },
                        "stages": []
                    }
                },
                stop_template: {
                    stop_content: {
                        "title": { 
                             "placeholder": null
                        },
                        "stages": [{
                                "text": {
                                    "English": "Navigation"
                                },
                                "type": "separator"
                            },
                            {
                                "text": {
                                    "placeholder": null
                                },
                                "type": "navigation",
                                "buttonTitle": {
                                    "English": "Show Map"
                                },
                                "targetPoint": null
                            },
                            {
                                "text": {
                                    "English": "Guide"
                                },
                                "type": "separator"
                            },
                            {
                                "text": {
                                    "placeholder": null
                                },
                                "type": "guide"
                            }
                        ]
                    }
                }
            }
        },
        computed: {
            previewLink: function () {

                return "/tour/" + this.tour.id + "/" + this.stop.sort_order;
            }
        },
        methods: {
            addStage: function () {
                this.stop.stop_content.stages.push({
                    "type": this.newStageType
                });
                this.newStageType = null;
            },
            save: function () {
                if (!this.stop.id) {
                    axios.post("/creator/edit/" + this.tour.id + "/stop/", this.stop)
                        .then((res) => {
                            this.stop.id = res.data.id;
                            this.stop.sort_order = res.data.sort_order;
                            this.$router.replace({
                                name: 'editStop',
                                params: {
                                    tourId: this.tourId,
                                    stopId: this.stop.id
                                }
                            })
                            this.showAlert = true;
                        });
                } else {
                    axios.put("/creator/edit/" + this.tour.id + "/stop/" + this.stop.id, this.stop)
                        .then((res) => {
                            this.showAlert = true;
                        });
                }
            }
        },
        mounted: function () {
            // cache bust because otherwise we won't reload the tour when using the back button
            axios.get("/creator/edit/" + this.tourId + "?" + Math.random())
                .then((res) => {
                    this.tour = res.data
                    if (this.stopId) {
                        this.stop = this.tour.stops.find(s => s.id == this.stopId);
                    } else if (this.tour.tour_content.use_template) {
                        this.stop = this.stop_template;
                    }
                });


        },
        // created: function() {
        //     if(!this.stop.title) {
        //         Vue.set(this.stop, "title", {});
        //         Vue.set(this.stop, "stages", []);
        //     }
        // }
    }
</script>