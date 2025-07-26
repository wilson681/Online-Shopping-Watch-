function toggleSection(id) {
    var content = document.getElementById(id);
    if (content.style.display === "block") {
        content.style.display = "none";
    } else {
        content.style.display = "block";
    }
}

// 搜索功能
function searchQuestions() {
    var searchTerm = document.getElementById('search-input').value.trim(); // 获取搜索词并去除首尾空格
    var resultsContainer = document.getElementById('search-results');

    // 清空之前的搜索结果（包括高亮）
    resultsContainer.innerHTML = "";
    resultsContainer.style.display = "block"; // 确保搜索结果区域显示

    if (searchTerm === "") {
        // 如果搜索词为空，重新加载 help.php 页面
        window.location.reload();
        return; // 停止搜索
    }

    var sections = document.querySelectorAll('.section');
    var searchTermLower = searchTerm.toLowerCase();

    sections.forEach(function(section) {
        var header = section.querySelector('.section-header').textContent.toLowerCase();
        var content = section.querySelector('.section-content').textContent.toLowerCase();

        if (header.includes(searchTermLower) || content.includes(searchTermLower)) {
            // 克隆匹配的部分并添加到搜索结果区域
            var clone = section.cloneNode(true);
            resultsContainer.appendChild(clone);
            clone.querySelector('.section-content').style.display = "block"; // 显示内容
            highlightText(clone.querySelector('.section-content'), searchTerm); // 高亮关键词
        }
    });

    // 如果没有匹配的结果，显示提示信息
    if (resultsContainer.innerHTML === "") {
        resultsContainer.innerHTML = "<p>No results found. Please try another search term.</p>";
    }
}

// 高亮显示关键词
function highlightText(element, searchTerm) {
    var content = element.innerHTML;
    var regex = new RegExp(searchTerm, 'gi');
    var highlightedContent = content.replace(regex, function(match) {
        return '<span class="highlight">' + match + '</span>';
    });
    element.innerHTML = highlightedContent;
}

// 页面加载时隐藏搜索结果区域 (确保初始状态不显示)
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('search-results').style.display = "none";
    // 初始状态下隐藏所有 section-content
    var allContents = document.querySelectorAll('.section-content');
    allContents.forEach(function(content) {
        content.style.display = "none";
    });
});

// 监听搜索按钮的点击事件
document.querySelector('.search-button').addEventListener('click', searchQuestions);

// 可选：如果你还希望在输入框内容为空时按下 Enter 键也刷新页面
document.getElementById('search-input').addEventListener('keypress', function(event) {
    if (event.key === 'Enter' && this.value.trim() === "") {
        window.location.reload();
    }
});