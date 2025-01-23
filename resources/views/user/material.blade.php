@extends('layouts.app')

@section('title', 'Material')

@section('content')
<div class="container-2 mt-4 p-3">
    <div>
        <h3 class="title">
            Activity 4
        </h3>
        <p class="author-date">
            John Smith • <span class="fw-bold">Dec 12, 2025</span>
        </p>
        <div class="d-flex justify-content-between">
            <p class="grade">47/50</p>
            <p class="text-muted text-start">Due Date: <span class="fw-bold">Dec 13, 2025</span></p>
        </div>
    </div>
    <div class="line my-2"></div>
    <div class="mt-5 p-2">
        <h6>
            Answer the following questions below.
        </h6>
        <ul class="list-unstyled" style="line-height: 32px;font-size:14px">
            <li>  
                1. How do science and technology influence societal development, and how does society shape the trajectory of scientific and technological innovation?
            </li>
            <li>  
                2. What are the ethical challenges posed by emerging technologies such as artificial intelligence, biotechnology, or autonomous systems, and how can they be addressed?
            </li>
            <li>  
                3. In what ways has technological advancement contributed to both reducing and exacerbating social inequality?
            </li>
            <li>  
                4. What is the role of government in funding, regulating, and directing the development of science and technology, and how does this affect innovation?
            </li>
            <li>  
                5. How can technological innovation be leveraged to address environmental challenges, and what are the potential trade-offs?
            </li>
            <li>  
                6. How have key historical events or periods shaped the relationship between science, technology, and society?
            </li>
            <li>  
                6. How have key historical events or periods shaped the relationship between science, technology, and society?
            </li>
            <li>  
                6. How have key historical events or periods shaped the relationship between science, technology, and society?
            </li>
            <li>  
                7. How has globalization affected the dissemination and impact of science and technology across different societies?
            </li>
            <li>  
                8. Why is effective communication of scientific knowledge important, and how can scientists address public mistrust or misunderstanding of science?
            </li>
            <li>  
                9. How do gender and cultural diversity impact scientific research and technological development?
            </li>
            <li>  
                10. How has the advancement of digital technologies challenged traditional notions of privacy, and what policies or frameworks are necessary to protect individual rights?
            </li>
        </ul>
        <div class="shadow-3 p-4 rounded">
            <p>Your Answer:</p>
            <form action="">
                <textarea class="p-3 mt-3 text-dark" style="border:none;background-color:#f1f1f1;outline:none;width:100%;height:300px;text-align:start;vertical-align:top;resize:none;font-size:14px"></textarea>
                <input type="submit" class="btn btn-primary mt-3 w-100" value="Submit">
            </form>
        </div>
        <div class="shadow-3 mt-3 p-4 rounded">
            <div class="d-flex justify-content-between align-items-center p-2">
                <input type="text" style="border:none;outline:none;width:100%;" placeholder="Class Comment">
                <i class="fa-solid fa-paper-plane cursor-pointer"></i>
            </div>
           
            <div class="line"></div>
            <p class="text-small mt-2">Comment in the class</p>
            <div class="relative cursor-pointer m-2">
                <i class="fa-regular fa-comment"></i>
                <span>1</span>
            </div>
           
           
        </div>
       
    </div>
</div>
@endsection
