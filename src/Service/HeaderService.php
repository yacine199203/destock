<?php
namespace App\Service;
use App\Repository\JobRepository;
use App\Repository\CategoryRepository;

class HeaderService
{
    protected $categoryRepo;
    protected $jobRepo;

    public function __construct(CategoryRepository $categoryRepo,JobRepository $jobRepo)
    {
        $this->categoryRepo = $categoryRepo;
        $this->jobRepo = $jobRepo;
    }

    public function categorys()
    {
        $categorys = $this->categoryRepo->findAll();
        return $categorys;
    }

    public function jobs()
    {
        $jobs = $this->jobRepo->findAll();
        return $jobs;
    }
}