// 非同期でいいね機能を実装

// 全てのいいねボタン(ハートアイコン)の要素を取得
const favoriteBtns = document.querySelectorAll(".favorite-btn");

favoriteBtns.forEach((favoriteBtn) => {
    // どれかのいいねボタンがクリックされた時の処理
    favoriteBtn.addEventListener("click", async (e) => {
        // クリックされたいいねボタンのid(post id)を取得
        const postId = e.target.id; 

        // /posts/{postId}/favoriteにPOSTリクエストを送信
        await fetch(`/posts/${postId}/favorite`, {
            method: "POST", // リクエストメソッドはPOST
            headers: {
                //Content-Typeでサーバーに送るデータの種類を伝える。今回はapplication/json
                "Content-Type": "application/json",
                //csrfトークンを付与
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
            .then((res) => res.json()) // レスポンスをJSON形式に変換;
            .then((data) => {
                // いいねの数を更新
                e.target.nextElementSibling.innerHTML = data.favoritesCount;
                
                // いいねの状態によってハートアイコンの色を変更
                if (e.target.classList.contains("text-pink-500")) {
                    e.target.classList.remove("text-pink-500");
                    e.target.setAttribute("name", "heart-outline");
                } else {
                    e.target.classList.add("text-pink-500");
                    e.target.setAttribute("name", "heart");
                }
            })
             // 失敗した場合はアラートを表示
            .catch(() => alert("いいね処理が失敗しました"));
    });
});

