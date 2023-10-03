//入力制限:読み込みの関係でbody内部に入れないとエラー発生する模様。
document.addEventListener('DOMContentLoaded', () => {
  // 入力制限と残り文字数表示
  const limitTextLength = () => {
    const maxLength = 140; // 文字数の上限
    const textcounter = document.getElementsByClassName('textlimit')[0];
    const remainingcounter = document.getElementsByClassName('numcounter')[0];
    const submitBtn = document.querySelector('.submit-btn');

    // 文字数制限を解除してマイナスの残り文字数を表示
    if (textcounter.value.length > maxLength) {
      remainingcounter.classList.add('max');
      remainingcounter.textContent = maxLength - textcounter.value.length;
    } else {
      remainingcounter.classList.remove('max');
      remainingcounter.textContent = maxLength - textcounter.value.length;
    }

    // 文字数が140文字を超えた場合は残り文字数を赤色にする
    if (textcounter.value.length > maxLength) {
      remainingcounter.style.color = 'red';
    } else {
      remainingcounter.style.color = ''; // 色を戻す
    }

    // 140字超えたらボタンOFF
    submitBtn.disabled = textcounter.value.length > maxLength;
  };

  // ページ読み込み時にカウンターを初期化
  limitTextLength();

  // 入力フィールドが変更されたらカウンターを更新
  document.getElementsByClassName('textlimit')[0].addEventListener('input', limitTextLength);
});

//削除を押すと確認アラームが発生する
let deleteLinks = document.getElementsByClassName('delete');
for (let i = 0; i < deleteLinks.length; i++) {
  deleteLinks[i].addEventListener('click', function () {
    const result = confirm('本当に削除してもよろしいですか?');
    if (result) {
      const postId = this.getAttribute('data-post-id');
      window.location.href = "delete.php?id=" + postId;
    } else {
      alert('キャンセルされました');
    }
  });
}

// すべてのページに適用
//*スクロール絵文字
//*ボタン
const scroll_to_top_btn = document.querySelector('.up');
//*クリックイベントを追加
scroll_to_top_btn.addEventListener('click', scroll_to_top);

function scroll_to_top() {
  window.scroll({
    top: 0,
    behavior: 'smooth'
  });
};
//*スクロール時のイベントを追加
window.addEventListener('scroll', scroll_event);

function scroll_event() {

  if (window.pageYOffset > 400) {
    scroll_to_top_btn.style.opacity = '1';
  } else if (window.pageYOffset < 400) {
    scroll_to_top_btn.style.opacity = '0';
  }
};