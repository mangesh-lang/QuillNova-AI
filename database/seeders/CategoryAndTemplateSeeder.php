<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Template;
use Illuminate\Database\Seeder;

class CategoryAndTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Categories
        $categoriesData = [
            [
                'name' => 'SEO & Content',
                'slug' => 'seo-content',
                'description' => 'Optimize search rankings and generate high-traffic blog posts.',
                'icon' => 'bi-search',
            ],
            [
                'name' => 'Marketing & Social Media',
                'slug' => 'marketing-social',
                'description' => 'Craft engaging campaigns, ad copy, and social media posts.',
                'icon' => 'bi-megaphone',
            ],
            [
                'name' => 'Business & Copywriting',
                'slug' => 'business-copywriting',
                'description' => 'Generate business names, professional emails, and taglines.',
                'icon' => 'bi-briefcase',
            ],
            [
                'name' => 'Programming & Dev Tools',
                'slug' => 'programming-dev',
                'description' => 'Write clean code, debug syntax, generate SQL, and explain logic.',
                'icon' => 'bi-code-slash',
            ],
            [
                'name' => 'Personal & Career',
                'slug' => 'personal-career',
                'description' => 'Build high-converting resumes, cover letters, and write stories.',
                'icon' => 'bi-person-badge',
            ],
            [
                'name' => 'Education & Utilities',
                'slug' => 'education-utilities',
                'description' => 'Check grammar, summarize long texts, translate languages, and generate MCQs.',
                'icon' => 'bi-book',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'icon' => $cat['icon'],
                    'is_active' => true
                ]
            );
        }

        // 2. Seed Templates (AI Tools)
        $templatesData = [
            // --- SEO & Content ---
            [
                'category_slug' => 'seo-content',
                'name' => 'Blog Generator',
                'slug' => 'blog-generator',
                'description' => 'Generate a complete, structured, high-quality blog post.',
                'icon' => 'bi-journal-text',
                'prompt_template' => "Write a structured and engaging blog post about the topic: \"{topic}\". Use the following keywords: \"{keywords}\". Write in a \"{tone}\" tone. Language: {language}. Make the article around {length} words, including introduction, headings, structured body paragraphs, and a conclusion.",
                'fields' => [
                    ['name' => 'topic', 'type' => 'text', 'label' => 'Blog Topic', 'required' => true],
                    ['name' => 'keywords', 'type' => 'text', 'label' => 'Keywords (comma separated)', 'required' => false],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone of Voice', 'required' => true, 'options' => ['Professional', 'Informative', 'Friendly', 'Casual', 'Persuasive']],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French', 'German', 'Italian', 'Hindi']],
                    ['name' => 'length', 'type' => 'select', 'label' => 'Target Length', 'required' => true, 'options' => ['500', '1000', '1500']],
                ]
            ],
            [
                'category_slug' => 'seo-content',
                'name' => 'SEO Article Generator',
                'slug' => 'seo-article-generator',
                'description' => 'Create search engine optimized articles designed to rank on Google.',
                'icon' => 'bi-search-heart',
                'prompt_template' => "Create a highly SEO-optimized article about \"{topic}\". The primary keyword is \"{primary_keyword}\", and secondary keywords are \"{target_keywords}\". The target language is {language} and the tone should be \"{tone}\". Include HTML tags like <h2>, <h3>, list points, and write metadata (Title and Description) at the very top.",
                'fields' => [
                    ['name' => 'topic', 'type' => 'text', 'label' => 'Article Topic', 'required' => true],
                    ['name' => 'primary_keyword', 'type' => 'text', 'label' => 'Primary Keyword', 'required' => true],
                    ['name' => 'target_keywords', 'type' => 'text', 'label' => 'LSI / Secondary Keywords', 'required' => false],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Analytical', 'Conversational', 'Authoritative', 'Helpful']],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French', 'German', 'Hindi']],
                ]
            ],
            [
                'category_slug' => 'seo-content',
                'name' => 'FAQ Generator',
                'slug' => 'faq-generator',
                'description' => 'Generate Frequently Asked Questions (FAQs) with rich answers.',
                'icon' => 'bi-question-circle',
                'prompt_template' => "Generate {number_of_faqs} frequently asked questions (FAQs) with detailed, helpful answers about \"{topic}\" or service \"{product_service}\". Present them as questions followed by answers, optimized for search featured snippets.",
                'fields' => [
                    ['name' => 'topic', 'type' => 'text', 'label' => 'Core Subject/Topic', 'required' => true],
                    ['name' => 'product_service', 'type' => 'text', 'label' => 'Product or Service Name (Optional)', 'required' => false],
                    ['name' => 'number_of_faqs', 'type' => 'select', 'label' => 'Number of FAQs', 'required' => true, 'options' => ['3', '5', '8', '10']],
                ]
            ],

            // --- Marketing & Social Media ---
            [
                'category_slug' => 'marketing-social',
                'name' => 'YouTube Script Generator',
                'slug' => 'youtube-script-generator',
                'description' => 'Generate an engaging YouTube video script outlines, hook, and body.',
                'icon' => 'bi-youtube',
                'prompt_template' => "Write a YouTube video script about: \"{video_topic}\". Target audience is \"{target_audience}\". Tone: \"{tone}\". Language: {language}. Structure the script with a powerful Hook, an Intro, Main Points (with visual cues), and a strong Call to Action (CTA).",
                'fields' => [
                    ['name' => 'video_topic', 'type' => 'text', 'label' => 'Video Topic', 'required' => true],
                    ['name' => 'target_audience', 'type' => 'text', 'label' => 'Target Audience', 'required' => true],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Energetic', 'Educational', 'Storyteller', 'Serious', 'Witty']],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French', 'Hindi']],
                ]
            ],
            [
                'category_slug' => 'marketing-social',
                'name' => 'Instagram Caption',
                'slug' => 'instagram-caption',
                'description' => 'Write trendy, engaging Instagram captions with emojis and hashtags.',
                'icon' => 'bi-instagram',
                'prompt_template' => "Write 3 alternative Instagram captions based on this description: \"{photo_description}\". Tone: \"{tone}\". Include relevant emojis. Include hashtags: {hashtags}. Language: {language}.",
                'fields' => [
                    ['name' => 'photo_description', 'type' => 'textarea', 'label' => 'What is the image/video about?', 'required' => true],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Sassy', 'Inspiring', 'Funny', 'Minimalist', 'Informative']],
                    ['name' => 'hashtags', 'type' => 'select', 'label' => 'Include Hashtags?', 'required' => true, 'options' => ['Yes', 'No']],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French', 'German']],
                ]
            ],
            [
                'category_slug' => 'marketing-social',
                'name' => 'Facebook Post',
                'slug' => 'facebook-post',
                'description' => 'Generate high-CTR social media copy for Facebook.',
                'icon' => 'bi-facebook',
                'prompt_template' => "Create an engaging Facebook post about: \"{post_topic}\". The main goal is \"{goal}\". Tone: \"{tone}\". Include a hook, spacing for readability, a clear call-to-action, and relevant emojis. Language: {language}.",
                'fields' => [
                    ['name' => 'post_topic', 'type' => 'textarea', 'label' => 'What is the post about?', 'required' => true],
                    ['name' => 'goal', 'type' => 'select', 'label' => 'Goal of the Post', 'required' => true, 'options' => ['Increase Engagement', 'Drive Website Traffic', 'Promote a Product', 'Share Information']],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Friendly', 'Professional', 'Excited', 'Thoughtful']],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French']],
                ]
            ],
            [
                'category_slug' => 'marketing-social',
                'name' => 'LinkedIn Post',
                'slug' => 'linkedin-post',
                'description' => 'Write thought-leadership style posts for professionals on LinkedIn.',
                'icon' => 'bi-linkedin',
                'prompt_template' => "Write a professional LinkedIn post about: \"{topic}\". The objective is \"{goal}\". Tone: \"{tone}\". Format it with a hook sentence, space breaks, key take-aways, and professional hashtags. Language: {language}.",
                'fields' => [
                    ['name' => 'topic', 'type' => 'textarea', 'label' => 'Topic/Insight', 'required' => true],
                    ['name' => 'goal', 'type' => 'select', 'label' => 'Goal', 'required' => true, 'options' => ['Personal Brand building', 'Promote Job Opening', 'Product Launch', 'Industry News']],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Professional', 'Inspirational', 'Casual', 'Intellectual']],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French']],
                ]
            ],
            [
                'category_slug' => 'marketing-social',
                'name' => 'Twitter/X Post',
                'slug' => 'twitter-post',
                'description' => 'Draft viral tweets or thread starters under character limits.',
                'icon' => 'bi-twitter-x',
                'prompt_template' => "Write 3 alternative tweets (under 280 characters each) about \"{topic}\". Tone should be \"{tone}\". Include emojis: {include_emojis}. Make them engaging, punchy, and modern.",
                'fields' => [
                    ['name' => 'topic', 'type' => 'text', 'label' => 'Tweet Topic', 'required' => true],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Punchy', 'Informative', 'Humorous', 'Controversial', 'Professional']],
                    ['name' => 'include_emojis', 'type' => 'select', 'label' => 'Include Emojis?', 'required' => true, 'options' => ['Yes', 'No']],
                ]
            ],
            [
                'category_slug' => 'marketing-social',
                'name' => 'Slogan Generator',
                'slug' => 'slogan-generator',
                'description' => 'Generate memorable slogans for your brand or campaign.',
                'icon' => 'bi-chat-quote',
                'prompt_template' => "Generate 10 catchy slogans for a business/brand described as: \"{business_description}\". Use keywords: \"{keywords}\". Tone: \"{tone}\". Make them short, memorable, and creative.",
                'fields' => [
                    ['name' => 'business_description', 'type' => 'textarea', 'label' => 'Brand Description', 'required' => true],
                    ['name' => 'keywords', 'type' => 'text', 'label' => 'Keywords to Include', 'required' => false],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Catchy & Energetic', 'Corporate & Trustworthy', 'Luxury & Elegant', 'Funny & Playful']],
                ]
            ],
            [
                'category_slug' => 'marketing-social',
                'name' => 'Tagline Generator',
                'slug' => 'tagline-generator',
                'description' => 'Create compelling brand taglines to define your mission.',
                'icon' => 'bi-card-heading',
                'prompt_template' => "Create 5 professional brand taglines based on: \"{product_description}\" reflecting the core brand essence: \"{brand_essence}\". Tone: \"{tone}\".",
                'fields' => [
                    ['name' => 'product_description', 'type' => 'text', 'label' => 'Product/Service Description', 'required' => true],
                    ['name' => 'brand_essence', 'type' => 'text', 'label' => 'Brand Core Message/Values', 'required' => true],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Inspiring', 'Minimalist', 'Clever', 'Corporate']],
                ]
            ],

            // --- Business & Copywriting ---
            [
                'category_slug' => 'business-copywriting',
                'name' => 'Product Description',
                'slug' => 'product-description',
                'description' => 'Generate persuasive product descriptions that sell.',
                'icon' => 'bi-box-seam',
                'prompt_template' => "Write a persuasive product description for \"{product_name}\". Key features: \"{key_features}\". Target audience is \"{target_audience}\". Write in a \"{tone}\" tone. Language: {language}.",
                'fields' => [
                    ['name' => 'product_name', 'type' => 'text', 'label' => 'Product Name', 'required' => true],
                    ['name' => 'key_features', 'type' => 'textarea', 'label' => 'Key Features/Benefits', 'required' => true],
                    ['name' => 'target_audience', 'type' => 'text', 'label' => 'Target Audience', 'required' => false],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Selling & Eager', 'Professional', 'Luxury', 'Casual']],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French', 'German']],
                ]
            ],
            [
                'category_slug' => 'business-copywriting',
                'name' => 'Email Writer',
                'slug' => 'email-writer',
                'description' => 'Draft professional or custom emails for any situation.',
                'icon' => 'bi-envelope-paper',
                'prompt_template' => "Write an email to \"{recipient}\". Context: \"{context}\". Purpose: \"{purpose}\". Tone should be \"{tone}\". Language: {language}. Provide a Subject line and clean layout.",
                'fields' => [
                    ['name' => 'recipient', 'type' => 'text', 'label' => 'Recipient (e.g. Manager, Client)', 'required' => true],
                    ['name' => 'context', 'type' => 'textarea', 'label' => 'Context/Background Info', 'required' => true],
                    ['name' => 'purpose', 'type' => 'text', 'label' => 'Purpose (e.g. Request extension, Follow up)', 'required' => true],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Professional', 'Polite', 'Direct', 'Urgent', 'Warm']],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French', 'German']],
                ]
            ],
            [
                'category_slug' => 'business-copywriting',
                'name' => 'Business Name Generator',
                'slug' => 'business-name-generator',
                'description' => 'Brainstorm creative, professional names for startup or business.',
                'icon' => 'bi-patch-check',
                'prompt_template' => "Generate 15 startup/business name ideas for the industry \"{industry}\". Keywords/Theme: \"{keywords}\". Name style should be \"{style_name}\". For each name, write a 1-sentence brand concept.",
                'fields' => [
                    ['name' => 'industry', 'type' => 'text', 'label' => 'Industry/Niche', 'required' => true],
                    ['name' => 'keywords', 'type' => 'text', 'label' => 'Keywords or Core Idea', 'required' => true],
                    ['name' => 'style_name', 'type' => 'select', 'label' => 'Name Style', 'required' => true, 'options' => ['Modern & Techy', 'Classic & Elegant', 'Short & Compound', 'Creative & Abstract']],
                ]
            ],

            // --- Programming & Dev Tools ---
            [
                'category_slug' => 'programming-dev',
                'name' => 'Code Generator',
                'slug' => 'code-generator',
                'description' => 'Generate clean programming code based on requirements.',
                'icon' => 'bi-file-earmark-code',
                'prompt_template' => "Write code based on the description: \"{description}\". Language: \"{programming_language}\". Framework (if any): \"{framework}\". Write clean, syntactically correct, and commented code.",
                'fields' => [
                    ['name' => 'description', 'type' => 'textarea', 'label' => 'What should the code do?', 'required' => true],
                    ['name' => 'programming_language', 'type' => 'text', 'label' => 'Language (e.g. PHP, JS, Python)', 'required' => true],
                    ['name' => 'framework', 'type' => 'text', 'label' => 'Framework (e.g. Laravel, React) (Optional)', 'required' => false],
                ]
            ],
            [
                'category_slug' => 'programming-dev',
                'name' => 'Code Explainer',
                'slug' => 'code-explainer',
                'description' => 'Analyze and explain code logic in detail.',
                'icon' => 'bi-info-circle',
                'prompt_template' => "Explain the following code snippet. Explain it with a \"{detail_level}\" level of detail. Explain the logic step-by-step, the time complexity (if applicable), and list any potential edge cases.\n\nCode:\n{code}",
                'fields' => [
                    ['name' => 'code', 'type' => 'textarea', 'label' => 'Paste your Code', 'required' => true],
                    ['name' => 'detail_level', 'type' => 'select', 'label' => 'Detail Level', 'required' => true, 'options' => ['Medium', 'High (Line-by-Line)', 'Low (Summary)']],
                ]
            ],
            [
                'category_slug' => 'programming-dev',
                'name' => 'Code Debugger',
                'slug' => 'code-debugger',
                'description' => 'Locate syntax or logical bugs in code and output fixes.',
                'icon' => 'bi-bug',
                'prompt_template' => "Find bugs and syntax errors in this \"{language}\" code. Error message (if any): \"{error_message}\". Explain what is wrong, output the corrected version of the code, and explain the fix.\n\nCode:\n{code}",
                'fields' => [
                    ['name' => 'code', 'type' => 'textarea', 'label' => 'Code with Bugs', 'required' => true],
                    ['name' => 'language', 'type' => 'text', 'label' => 'Programming Language', 'required' => true],
                    ['name' => 'error_message', 'type' => 'text', 'label' => 'Error Message (Optional)', 'required' => false],
                ]
            ],
            [
                'category_slug' => 'programming-dev',
                'name' => 'SQL Query Generator',
                'slug' => 'sql-query-generator',
                'description' => 'Generate SQL queries based on database requirements.',
                'icon' => 'bi-database-fill-gear',
                'prompt_template' => "Write a SQL query in \"{database_dialect}\" dialect. \nSchema information:\n\"{table_schema}\"\nRequirement:\n\"{requirement}\". Output only SQL query and a short comment explanation.",
                'fields' => [
                    ['name' => 'requirement', 'type' => 'textarea', 'label' => 'What query do you need to write?', 'required' => true],
                    ['name' => 'table_schema', 'type' => 'textarea', 'label' => 'Table Schemas / Columns (Optional)', 'required' => false],
                    ['name' => 'database_dialect', 'type' => 'select', 'label' => 'Database Dialect', 'required' => true, 'options' => ['MySQL', 'PostgreSQL', 'SQLite', 'SQL Server']],
                ]
            ],
            [
                'category_slug' => 'programming-dev',
                'name' => 'Regex Generator',
                'slug' => 'regex-generator',
                'description' => 'Generate Regular Expressions (Regex) matching your criteria.',
                'icon' => 'bi-regex',
                'prompt_template' => "Generate a regular expression (regex) that: \"{pattern_description}\". Include test strings to match: \"{test_strings}\". Output the regex pattern, explanation of each capture group/symbol, and examples in JavaScript or PHP.",
                'fields' => [
                    ['name' => 'pattern_description', 'type' => 'text', 'label' => 'What should the pattern match? (e.g. Email, phone)', 'required' => true],
                    ['name' => 'test_strings', 'type' => 'text', 'label' => 'Example Strings to Match (comma separated)', 'required' => false],
                ]
            ],

            // --- Personal & Career ---
            [
                'category_slug' => 'personal-career',
                'name' => 'Resume Builder',
                'slug' => 'resume-builder',
                'description' => 'Compile standard formatting for resume bullet points and summary.',
                'icon' => 'bi-file-person',
                'prompt_template' => "Generate a professional resume layout for Candidate: \"{full_name}\". Professional Summary: \"{professional_summary}\". Work Experience: \"{experience}\". Key Skills: \"{skills}\". Education: \"{education}\". Format it cleanly using Markdown.",
                'fields' => [
                    ['name' => 'full_name', 'type' => 'text', 'label' => 'Full Name', 'required' => true],
                    ['name' => 'professional_summary', 'type' => 'textarea', 'label' => 'Professional Summary / Role', 'required' => true],
                    ['name' => 'experience', 'type' => 'textarea', 'label' => 'Work Experience (Details & Years)', 'required' => true],
                    ['name' => 'skills', 'type' => 'text', 'label' => 'Skills (comma separated)', 'required' => true],
                    ['name' => 'education', 'type' => 'text', 'label' => 'Education & Qualifications', 'required' => true],
                ]
            ],
            [
                'category_slug' => 'personal-career',
                'name' => 'Cover Letter Generator',
                'slug' => 'cover-letter-generator',
                'description' => 'Write tailored cover letters for specific job applications.',
                'icon' => 'bi-file-earmark-text',
                'prompt_template' => "Write a high-converting, tailored cover letter for a \"{job_title}\" position at \"{company_name}\". The candidate has these key skills: \"{skills}\" and experience: \"{experience_summary}\". Write in \"{language}\". Make it professional, persuasive, and outline why they are a perfect fit.",
                'fields' => [
                    ['name' => 'job_title', 'type' => 'text', 'label' => 'Job Title', 'required' => true],
                    ['name' => 'company_name', 'type' => 'text', 'label' => 'Company Name', 'required' => true],
                    ['name' => 'experience_summary', 'type' => 'textarea', 'label' => 'Summary of Experience', 'required' => true],
                    ['name' => 'skills', 'type' => 'text', 'label' => 'Top 3 Skills', 'required' => true],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French']],
                ]
            ],
            [
                'category_slug' => 'personal-career',
                'name' => 'Interview Questions Generator',
                'slug' => 'interview-questions-generator',
                'description' => 'Generate interview questions based on job description.',
                'icon' => 'bi-chat-left-dots',
                'prompt_template' => "Generate 10 relevant interview questions (with sample answer tips) for the role of \"{job_role}\". Candidate experience level: \"{experience_level}\". Company type: \"{company_type}\". Include 5 technical/functional questions and 5 behavioral questions.",
                'fields' => [
                    ['name' => 'job_role', 'type' => 'text', 'label' => 'Job Role / Title', 'required' => true],
                    ['name' => 'experience_level', 'type' => 'select', 'label' => 'Experience Level', 'required' => true, 'options' => ['Junior', 'Mid-Level', 'Senior', 'Manager']],
                    ['name' => 'company_type', 'type' => 'text', 'label' => 'Company Type (e.g. Startup, Enterprise)', 'required' => false],
                ]
            ],
            [
                'category_slug' => 'personal-career',
                'name' => 'Story Generator',
                'slug' => 'story-generator',
                'description' => 'Generate original, creative stories based on details.',
                'icon' => 'bi-book-half',
                'prompt_template' => "Write an original story based on this idea: \"{story_idea}\". Genre: \"{genre}\". Main characters: \"{main_characters}\". Length: {length}. Write in a creative and captivating literary style.",
                'fields' => [
                    ['name' => 'story_idea', 'type' => 'textarea', 'label' => 'Story Premise / Idea', 'required' => true],
                    ['name' => 'genre', 'type' => 'select', 'label' => 'Genre', 'required' => true, 'options' => ['Science Fiction', 'Fantasy', 'Mystery', 'Adventure', 'Drama']],
                    ['name' => 'main_characters', 'type' => 'text', 'label' => 'Characters (comma separated)', 'required' => true],
                    ['name' => 'length', 'type' => 'select', 'label' => 'Length', 'required' => true, 'options' => ['Short (300 words)', 'Medium (600 words)', 'Detailed (1000 words)']],
                ]
            ],
            [
                'category_slug' => 'personal-career',
                'name' => 'Poem Generator',
                'slug' => 'poem-generator',
                'description' => 'Generate rhythmic poems or verses in different styles.',
                'icon' => 'bi-heart',
                'prompt_template' => "Write a poem about the topic: \"{topic}\". Style: \"{style}\". Tone should be \"{tone}\". Use vivid metaphors and capture emotional resonance.",
                'fields' => [
                    ['name' => 'topic', 'type' => 'text', 'label' => 'Poem Topic', 'required' => true],
                    ['name' => 'style', 'type' => 'select', 'label' => 'Poetic Style', 'required' => true, 'options' => ['Free Verse', 'Sonnet', 'Haiku', 'Limerick']],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Tone', 'required' => true, 'options' => ['Melancholic', 'Joyful', 'Romantic', 'Mysterious', 'Inspirational']],
                ]
            ],

            // --- Education & Utilities ---
            [
                'category_slug' => 'education-utilities',
                'name' => 'Grammar Checker',
                'slug' => 'grammar-checker',
                'description' => 'Proofread text for spelling, punctuation, and structural errors.',
                'icon' => 'bi-check-all',
                'prompt_template' => "Analyze and correct grammar, spelling, and punctuation errors in the following text. Tone should be \"{tone}\". Output the corrected version first under the header '## Corrected Text', followed by a list of explanations of what was fixed under the header '## Explanations'.\n\nText:\n\"{text_to_check}\"",
                'fields' => [
                    ['name' => 'text_to_check', 'type' => 'textarea', 'label' => 'Text to Check', 'required' => true],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'Adjusted Tone (if applicable)', 'required' => true, 'options' => ['No Tone Change', 'Professional', 'Academic', 'Casual']],
                ]
            ],
            [
                'category_slug' => 'education-utilities',
                'name' => 'Text Summarizer',
                'slug' => 'text-summarizer',
                'description' => 'Summarize long texts or documents into concise points.',
                'icon' => 'bi-body-text',
                'prompt_template' => "Summarize the following text. The summary should be \"{length}\" in length. Present the summary with bullet points highlighting the core information.\n\nText:\n{original_text}",
                'fields' => [
                    ['name' => 'original_text', 'type' => 'textarea', 'label' => 'Original Text', 'required' => true],
                    ['name' => 'length', 'type' => 'select', 'label' => 'Summary Length', 'required' => true, 'options' => ['Short (1-2 sentences)', 'Medium (3-5 bullet points)', 'Detailed Summary']],
                ]
            ],
            [
                'category_slug' => 'education-utilities',
                'name' => 'Text Rewriter',
                'slug' => 'text-rewriter',
                'description' => 'Rewrite or rephrase text to change tone or voice.',
                'icon' => 'bi-arrow-repeat',
                'prompt_template' => "Rewrite the following text. The rewritten version should maintain the original meaning but change the tone to \"{tone}\". Language: {language}.\n\nText:\n{original_text}",
                'fields' => [
                    ['name' => 'original_text', 'type' => 'textarea', 'label' => 'Original Text', 'required' => true],
                    ['name' => 'tone', 'type' => 'select', 'label' => 'New Tone', 'required' => true, 'options' => ['Professional', 'Simplified & Easy to Read', 'Creative', 'More Persuasive']],
                    ['name' => 'language', 'type' => 'select', 'label' => 'Language', 'required' => true, 'options' => ['English', 'Spanish', 'French', 'Hindi']],
                ]
            ],
            [
                'category_slug' => 'education-utilities',
                'name' => 'Translate Text',
                'slug' => 'translate-text',
                'description' => 'Translate text between multiple global languages.',
                'icon' => 'bi-translate',
                'prompt_template' => "Translate the following text to \"{target_language}\". Do not output anything other than the exact translation.\n\nText:\n{original_text}",
                'fields' => [
                    ['name' => 'original_text', 'type' => 'textarea', 'label' => 'Text to Translate', 'required' => true],
                    ['name' => 'target_language', 'type' => 'select', 'label' => 'Target Language', 'required' => true, 'options' => ['English', 'Spanish', 'French', 'German', 'Italian', 'Hindi', 'Japanese', 'Arabic']],
                ]
            ],
            [
                'category_slug' => 'education-utilities',
                'name' => 'MCQ Generator',
                'slug' => 'mcq-generator',
                'description' => 'Generate Multiple Choice Questions (MCQs) for tests.',
                'icon' => 'bi-list-check',
                'prompt_template' => "Generate {number_of_questions} Multiple Choice Questions (MCQs) about the topic \"{topic}\". Difficulty level: \"{difficulty}\". Present them as numbered questions, with 4 choices (A, B, C, D) each, and output the correct answers with brief explanations at the very bottom under the header '## Answer Key'.",
                'fields' => [
                    ['name' => 'topic', 'type' => 'text', 'label' => 'Subject / Topic', 'required' => true],
                    ['name' => 'difficulty', 'type' => 'select', 'label' => 'Difficulty', 'required' => true, 'options' => ['Easy', 'Medium', 'Hard']],
                    ['name' => 'number_of_questions', 'type' => 'select', 'label' => 'Number of Questions', 'required' => true, 'options' => ['3', '5', '10']],
                ]
            ],
            [
                'category_slug' => 'education-utilities',
                'name' => 'Prompt Improver',
                'slug' => 'prompt-improver',
                'description' => 'Enhance raw prompts to get better results from LLMs.',
                'icon' => 'bi-stars',
                'prompt_template' => "Improve the following raw prompt: \"{raw_prompt}\" to achieve this goal: \"{goal}\". Output a structured, contextual prompt that can be copy-pasted into ChatGPT or Gemini, followed by a brief 2-sentence explanation of what was added.",
                'fields' => [
                    ['name' => 'raw_prompt', 'type' => 'textarea', 'label' => 'Your Raw Prompt', 'required' => true],
                    ['name' => 'goal', 'type' => 'text', 'label' => 'Goal of the prompt (Optional)', 'required' => false],
                ]
            ],
        ];

        foreach ($templatesData as $temp) {
            $cat = $categories[$temp['category_slug']] ?? null;
            if ($cat) {
                Template::firstOrCreate(
                    ['slug' => $temp['slug']],
                    [
                        'name' => $temp['name'],
                        'description' => $temp['description'],
                        'category_id' => $cat->id,
                        'prompt_template' => $temp['prompt_template'],
                        'fields' => $temp['fields'],
                        'icon' => $temp['icon'],
                        'is_active' => true
                    ]
                );
            }
        }
    }
}
