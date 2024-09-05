<template>
  <inertia-head :title="program.weight_code" />

  <ProgramLayout :competitionId="program.competition_category.competition_id">
    <!-- Section, Mat sequences
    <a-switch v-model:checked="masterSequence" @change="rebuildBouts" />
    <br />
    <a-button :href="route('manage.competition.program.gen_bouts', program.id)"
      >Create Bouts</a-button
    > -->
    <div class="py-12 xl:mx-16 mx-8">
      <div class="overflow-hidden flex flex-col gap-3">
        <div class="grid grid-cols-4 gap-12 py-4">
          <a-card class="shadow-lg">
            <a-statistic title="Status" value="Ready to start" />
          </a-card>
          <a-card class="shadow-lg">
            <a-statistic title="Date" :value="program.date" />
          </a-card>
          <a-card class="shadow-lg">
            <a-statistic title="Programs" :value="program.bouts.length" />
          </a-card>
          <a-card class="shadow-lg">
            <a-statistic
              title="Atheles"
              :value="program.athletes ? program.athletes.length : 0"
            />
          </a-card>
        </div>
        <div class="grid grid-cols-4 gap-12">
          <div class="col-span-2 flex flex-col gap-6">
            <div class="py-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
              <div class="flex font-bold text-lg mb-2 justify-between">
                <div>Athletes list</div>
                <!-- <div>
                  <a-popconfirm
                    placement="rightTop"
                    ok-text="Yes"
                    cancel-text="No"
                    @confirm="joinAthlete(selectAthlete)"
                  >
                    <template #icon> </template>
                    <template #title>
                      <a-select
                        class="w-40"
                        v-model:value="selectAthlete"
                        :options="selectAthletes"
                      ></a-select>
                    </template>
                    <a-button type="primary" class="bg-blue-500">Add athletes</a-button>
                  </a-popconfirm>
                </div> -->
              </div>
              <a-table :dataSource="athletes" :columns="athleteColumns">
                <template #bodyCell="{ column, record }">
                  <template v-if="column.dataIndex === 'operation'">
                    <div class="space-x-2">
                      <a-popconfirm
                        title="Is to remove this athlete from the program?"
                        ok-text="Yes"
                        cancel-text="No"
                        @confirm="moveAthlete(record)"
                      >
                        <a-button>Delete</a-button>
                      </a-popconfirm>
                    </div>
                  </template>
                  <template v-else-if="column.dataIndex == 'is_weight_passed'">
                    {{ record.pivot.is_weight_passed }}
                  </template>
                  <template v-else-if="column.dataIndex == 'seed'">
                    {{ record.pivot.seed }}
                  </template>
                </template>
              </a-table>
            </div>
          </div>
          <div class="col-span-2 flex flex-col gap-6">
            <a-card class="w-full">
              <template #title><div class="font-normal">Competition result</div></template>
            </a-card>
            <a-card class="w-full" v-if="program.contest_system">
              <template #title>
                <div class="flex justify-between">
                  <div class="font-normal">Online table</div>
                  <div class="">
                    <a
                      :href="
                        route('manage.print.tournament_quarter', { program: program.id })
                      "
                      target="_blank"
                      >Print Pdf</a
                    >
                  </div>
                </div></template
              >
              <component
                v-if="program.bouts.length > 0"
                :is="tournamentTable"
                :contestSystem="program.contest_system"
                :bouts="bouts"
              />
            </a-card>
          </div>
        </div>
      </div>
    </div>
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import Tournament4 from "@/Components/TournamentTable/Elimination4.vue";
import Tournament8 from "@/Components/TournamentTable/Elimination8.vue";
import Tournament16 from "@/Components/TournamentTable/Elimination16.vue";
import Tournament32 from "@/Components/TournamentTable/Elimination32.vue";
import Tournament64 from "@/Components/TournamentTable/Elimination64.vue";

export default {
  components: {
    ProgramLayout,
    Tournament4,
    Tournament8,
    Tournament16,
    Tournament32,
    Tournament64,
  },
  props: ["competition", "program", "athletes"],
  data() {
    return {
      masterSequence: false,
      bouts: [],
      selectAthlete: "",
      selectAthletes: [],
      tournamentTable: "Tournament" + this.program.chart_size,
      dateFormat: "YYYY-MM-DD",
      playersList: [
        {
          name: "palyer 1",
          win: [1, 0],
        },
        {
          name: "palyer 2",
          win: [0, 0],
        },
        {
          name: "palyer 3",
          win: [1, 1],
        },
        {
          name: "palyer 4",
          win: [0, 0],
        },
      ],
      modal: {
        isOpen: false,
        mode: null,
        title: "Record Modal",
        data: {},
      },
      boutColumns: [
        {
          title: "In program Sequence",
          dataIndex: "in_program_sequence",
        },
        {
          title: "Sequence",
          dataIndex: "sequence",
        },
        {
          title: "Queue",
          dataIndex: "queue",
        },
        {
          title: "Round",
          dataIndex: "round",
        },
        {
          title: "White",
          dataIndex: "white",
        },
        {
          title: "Blue",
          dataIndex: "blue",
        },
        {
          title: "White from",
          dataIndex: "white_rise_from",
        },
        {
          title: "Blue from",
          dataIndex: "blue_rise_from",
        },
      ],
      athleteColumns: [
        {
          title: "Name Display",
          dataIndex: "name_display",
        },
        {
          title: "Gender",
          dataIndex: "gender",
        },
        {
          title: "Weigh Passed",
          dataIndex: "is_weight_passed",
        },
        {
          title: "Seed",
          dataIndex: "seed",
        },
        {
          title: "Operation",
          dataIndex: "operation",
        },
      ],
      rules: {
        country: { required: true },
        name: { required: true },
        date_start: { required: true },
        date_end: { required: true },
      },
      validateMessages: {
        required: "${label} is required!",
        types: {
          email: "${label} is not a valid email!",
          number: "${label} is not a valid number!",
        },
        number: {
          range: "${label} must be between ${min} and ${max}",
        },
      },
    };
  },
  created() {
    this.rebuildBouts();
    this.selectAthletes = this.athletes.map(function (x) {
      return {
        label: x.name,
        value: x.id,
      };
    });
  },
  methods: {
    rebuildBouts() {
      this.bouts = [];
      this.program.bouts.forEach((b) => {
        b.white_name_display = b.white_player.name_display;
        b.blue_name_display = b.blue_player.name_display;
        if (this.masterSequence) {
          b.circle = b.sequence;
        } else {
          b.circle = b.in_program_sequence;
        }
        this.bouts.push(b);
      });
      if (this.program.contest_system == "kos") {
        this.bouts.splice(this.program.chart_size - 2, 0, "");
        this.bouts.splice(this.program.chart_size - 1, 0, "");
        this.bouts.splice(this.program.chart_size - 4, 0, "");
        this.bouts.splice(this.program.chart_size - 3, 0, "");
      }
    },
    joinAthlete(athlete) {
      console.log(athlete);
      this.$inertia.post(
        route("manage.program.joinAthlete", {
          program: this.program.id,
          athlete: athlete,
        }),
        {
          onSuccess: (page) => {},
        }
      );
    },
    moveAthlete(athlete) {
      this.$inertia.delete(
        route("manage.program.removeAthlete", {
          program: this.program.id,
          athlete: athlete.id,
        }),
        {
          onSuccess: (page) => {},
        }
      );
    },
  },
};
</script>
