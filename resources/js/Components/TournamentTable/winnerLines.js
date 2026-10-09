// 讓上線表（Elimination4/8/16/32/64）把「勝者前進的路線」標成紅色。
//
// 這幾個元件的表格 HTML 是逐格寫死的，因此不改模板，改成「渲染後依實際座標比對」上色：
//   1. 第一輪：兩名選手合併處（firstColumn 內含 .circle 的格子）就是該場次的中線高度；
//      勝者是上面(白) → 上邊框紅；勝者是下面(藍) → 下邊框紅（class：win-top / win-bottom）。
//   2. 之後每一輪：含 .circle 的連接線格子代表「下一場」（target），
//      上邊框＝target 的白方來源場次（white_rise_from，畫面上在上方），
//      下方成對格子（同 x、top 等於本格 bottom）的下邊框＝target 的藍方來源場次（blue_rise_from）。
//   3. 冠軍（round = 1，沒有下一場）的最後一段線，取該場 circle 格子的下緣高度。
//   4. 最後把所有「線段高度 = 某場已分出勝負的中線高度」的連接線格子加上 win（紅線）。
//
// 上色用的 .win / .win-top / .win-bottom 樣式定義在 css/tournamentChart.css 與 Elimination4 的 scoped style。
function isDecided(bout) {
  return (
    !!bout &&
    bout.winner !== undefined &&
    bout.winner !== null &&
    bout.winner !== 0 &&
    bout.winner !== -1
  );
}

const CONNECTOR = /topRight|bottomRight|topOnly|bottomOnly|(^|\s)top(\s|$)/;

export default {
  mounted() {
    this.$nextTick(this.applyWinnerLines);
  },
  updated() {
    this.applyWinnerLines();
  },
  methods: {
    applyWinnerLines() {
      const table = this.$refs && this.$refs.tblTournament;
      if (!table) return;

      const bouts = (this.bouts || []).filter((b) => b && typeof b === "object");
      const byCircle = {};
      const bySeq = {};
      bouts.forEach((b) => {
        const key = b.circle !== undefined && b.circle !== null ? b.circle : b.in_program_sequence;
        if (key !== undefined && key !== null && key !== "") byCircle[String(key)] = b;
        if (b.in_program_sequence != null) bySeq[String(b.in_program_sequence)] = b;
      });

      const circleBout = (el) => {
        const c = el.querySelector(".circle");
        return c ? byCircle[c.textContent.trim()] : undefined;
      };

      table
        .querySelectorAll(".win, .win-top, .win-bottom")
        .forEach((el) => el.classList.remove("win", "win-top", "win-bottom"));

      // 每場「中線高度」：bout -> y
      const mergeY = new Map();

      // 1) 第一輪：合併處的格子（firstColumn 內的 innerTable 儲存格）
      table.querySelectorAll("td.firstColumn table.innerTable td").forEach((td) => {
        const b = circleBout(td);
        if (!b) return;
        const r = td.getBoundingClientRect();
        mergeY.set(b, r.top + r.height / 2);
        if (!isDecided(b)) return;
        if (b.winner === b.white) td.classList.add("win-top");
        else if (b.winner === b.blue) td.classList.add("win-bottom");
      });

      // 2) 連接線格子
      const cells = Array.from(
        table.querySelectorAll(":scope > tbody > tr > td, :scope > tr > td")
      )
        .filter((td) => CONNECTOR.test(td.className))
        .map((td) => {
          const cs = getComputedStyle(td);
          const r = td.getBoundingClientRect();
          return { td, r, bt: parseFloat(cs.borderTopWidth), bb: parseFloat(cs.borderBottomWidth) };
        })
        .filter((it) => it.bt > 0 || it.bb > 0);

      // 由含 circle 的格子推出前兩場的中線高度
      cells.forEach((it) => {
        if (!it.td.querySelector(".circle")) return;
        const target = circleBout(it.td);
        if (!target) return;
        if (it.bt > 0) {
          const upper = bySeq[String(target.white_rise_from)];
          if (upper) mergeY.set(upper, it.r.top);
        }
        if (it.bb > 0) {
          const lower = bySeq[String(target.blue_rise_from)];
          if (lower) mergeY.set(lower, it.r.bottom);
        }
      });

      // 成對（藍方來源）格子：同 x、top 等於含 circle 格子的下緣
      cells.forEach((it) => {
        if (it.td.querySelector(".circle")) return;
        const above = cells.find(
          (o) =>
            o.td.querySelector(".circle") &&
            Math.abs(o.r.left - it.r.left) < 1.5 &&
            Math.abs(o.r.bottom - it.r.top) < 1.5
        );
        if (!above) return;
        const target = circleBout(above.td);
        if (!target || it.bb <= 0) return;
        const lower = bySeq[String(target.blue_rise_from)];
        if (lower) mergeY.set(lower, it.r.bottom);
      });

      // 3) 冠軍（沒有下一場）的最後一段線
      const champion = bouts.find((b) => b.round === 1);
      if (champion) {
        const c = Array.from(table.querySelectorAll(".circle")).find(
          (el) => byCircle[el.textContent.trim()] === champion
        );
        const td = c && c.closest("td");
        if (td) mergeY.set(champion, td.getBoundingClientRect().bottom);
      }

      // 4) 依中線高度標紅
      const decidedYs = [];
      mergeY.forEach((y, b) => {
        if (isDecided(b)) decidedYs.push(y);
      });
      cells.forEach((it) => {
        const ys = [];
        if (it.bt > 0) ys.push(it.r.top);
        if (it.bb > 0) ys.push(it.r.bottom);
        if (ys.some((y) => decidedYs.some((dy) => Math.abs(dy - y) < 3))) {
          it.td.classList.add("win");
        }
      });
    },
  },
};
