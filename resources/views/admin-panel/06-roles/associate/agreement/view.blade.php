@extends('admin-panel.layouts.app')
@section('styles')
<style>
    .agreement-content-wrapper {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        color: #333;
    }
    
    .agreement-content-wrapper h1,
    .agreement-content-wrapper h2,
    .agreement-content-wrapper h3,
    .agreement-content-wrapper h4,
    .agreement-content-wrapper h5,
    .agreement-content-wrapper h6 {
        color: #2c3e50;
        margin-top: 20px;
        margin-bottom: 10px;
    }
    
    .agreement-content-wrapper p {
        margin-bottom: 15px;
        text-align: justify;
    }
    
    .agreement-content-wrapper table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    
    .agreement-content-wrapper table td,
    .agreement-content-wrapper table th {
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    .agreement-content-wrapper table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .pdf-preview-container {
        background: #f8f9fa;
        padding: 10px;
    }
    
    .card-toolbar {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
         .table th {
         background-color: #f8f9fa;
         font-weight: 600;
     }

     .comment-item {
         border-left: 3px solid #e9ecef;
     }

     .comment-item .card {
         border: 1px solid #dee2e6;
         box-shadow: 0 1px 3px rgba(0,0,0,0.1);
     }

     .comment-actions {
         opacity: 0.7;
         transition: opacity 0.3s;
     }

     .comment-item:hover .comment-actions {
         opacity: 1;
     }

     .replies-section {
         border-left: 2px solid #007bff;
         padding-left: 15px;
     }

     .avatar img {
         object-fit: cover;
     }

     .comment-card {
         transition: all 0.3s ease;
         border: 1px solid #e9ecef;
     }

     .comment-card:hover {
         box-shadow: 0 4px 8px rgba(0,0,0,0.1);
         transform: translateY(-2px);
     }

     .comment-text {
         line-height: 1.6;
         color: #495057;
     }
     
     .comment-text-section {
         background-color: #fff;
         border: 1px solid #e9ecef;
         border-radius: 8px;
         padding: 12px 16px;
         box-shadow: 0 1px 3px rgba(0,0,0,0.1);
         margin-top: 8px;
         transition: all 0.3s ease;
     }
     
     .comment-text-section:hover {
         box-shadow: 0 2px 6px rgba(0,0,0,0.15);
         border-color: #007bff;
     }
     
     .comment-text-section .comment-text {
         color: #495057;
         line-height: 1.6;
         margin: 0;
         font-size: 14px;
     }
     


     .add-comment-section .card {
         background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
         border: 1px solid #dee2e6;
     }

     .comment-actions .btn {
         transition: all 0.2s ease;
     }

     .comment-actions .btn:hover {
         transform: scale(1.05);
     }

     .replies-section {
         position: relative;
         margin-top: 15px;
     }

     .replies-section::before {
         content: '';
         position: absolute;
         left: -15px;
         top: 0;
         bottom: 0;
         width: 2px;
         background: linear-gradient(to bottom, #007bff, #6c757d);
     }
     
     .reply-item {
         margin-left: 20px;
         border-left: 3px solid #007bff;
         padding-left: 15px;
         margin-bottom: 10px;
     }
     
     .reply-item .card {
         background-color: #fff;
         border: 1px solid #e9ecef;
         box-shadow: 0 1px 3px rgba(0,0,0,0.1);
     }
     
     .reply-item .comment-text-section {
         background-color: #f8f9fa;
         border-color: #007bff;
     }
     
     .replies-section h6 {
         font-size: 0.875rem;
         color: #6c757d;
         border-bottom: 1px solid #e9ecef;
         padding-bottom: 8px;
         margin-bottom: 15px;
     }
     
     .reply-item .text-muted.small {
         font-size: 0.75rem;
         color: #6c757d;
         font-style: italic;
     }
     
     /* Comment with replies styling */
     .comment-with-replies {
         border: 1px solid #e9ecef;
         border-radius: 8px;
         padding: 15px;
         background-color: #fff;
     }
     
     .replies-to-comment {
         border-left: 3px solid #17a2b8;
         padding-left: 15px;
         margin-top: 15px;
     }
     
     .reply-header {
         color: #17a2b8;
         font-weight: 600;
         border-bottom: 1px solid #e9ecef;
         padding-bottom: 8px;
     }
     
     .reply-card {
         border-left: 4px solid #17a2b8;
         background-color: #f8f9fa;
     }
     
     .reply-context .badge {
         font-size: 0.75rem;
         padding: 5px 10px;
     }
     
     .reply-text-section {
         background-color: #fff;
         border: 1px solid #e9ecef;
         border-radius: 6px;
         padding: 10px 12px;
         margin-top: 8px;
     }
      
      .reply-item .avatar img {
          width: 30px !important;
          height: 30px !important;
      }
      
           .reply-item .comment-actions .btn {
         padding: 0.25rem 0.5rem;
         font-size: 0.75rem;
     }
     
     /* PDF Download Button Styling */
     #downloadPdfBtn:disabled {
         opacity: 0.7;
         cursor: not-allowed;
     }
     
     #downloadPdfBtn .fa-spinner {
         animation: spin 1s linear infinite;
     }
     
           @keyframes spin {
         0% { transform: rotate(0deg); }
         100% { transform: rotate(360deg); }
     }
     
     /* Modern Comment System Styling */
     .comments-header {
         border-bottom: 2px solid #e9ecef;
         padding-bottom: 15px;
     }
     
     .comments-header h5 {
         color: #2c3e50;
         font-weight: 600;
     }
     
     .comment-card {
         border: 1px solid #e9ecef;
         border-radius: 12px;
         box-shadow: 0 2px 8px rgba(0,0,0,0.08);
         transition: all 0.3s ease;
         background: #fff;
     }
     
     .comment-card:hover {
         box-shadow: 0 4px 16px rgba(0,0,0,0.12);
         transform: translateY(-2px);
     }
     
     .comment-item {
         position: relative;
     }
     
     .comment-item::before {
         content: '';
         position: absolute;
         left: -15px;
         top: 20px;
         bottom: 20px;
         width: 2px;
         background: linear-gradient(to bottom, #007bff, #6c757d);
         border-radius: 1px;
     }
     
     .avatar img {
         object-fit: cover;
         border: 2px solid #fff;
         box-shadow: 0 2px 4px rgba(0,0,0,0.1);
     }
     
     .bg-light-purple {
         background-color: #e6e6fa !important;
         color: #6a5acd !important;
         font-size: 0.75rem;
         padding: 4px 8px;
         border-radius: 12px;
         font-weight: 500;
     }
     
     .comment-text-section {
         background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
         border: 1px solid #e9ecef;
         border-radius: 10px;
         padding: 15px 18px;
         margin-top: 12px;
         transition: all 0.3s ease;
     }
     
     .comment-text-section:hover {
         box-shadow: 0 2px 8px rgba(0,0,0,0.1);
         border-color: #007bff;
     }
     
     .comment-text {
         color: #495057;
         line-height: 1.7;
         margin: 0;
         font-size: 14px;
     }
     
     .comment-actions {
         display: flex;
         gap: 8px;
         align-items: center;
         flex-wrap: wrap;
     }
     
     .comment-actions .btn {
         font-size: 0.8rem;
         padding: 6px 12px;
         border-radius: 20px;
         transition: all 0.2s ease;
         font-weight: 500;
     }
     
     .comment-actions .btn:hover {
         transform: translateY(-1px);
         box-shadow: 0 2px 6px rgba(0,0,0,0.15);
     }
     
     .reply-comment-btn {
         background: linear-gradient(135deg, #007bff, #0056b3);
         border: none;
         color: white;
     }
     
     .reply-comment-btn:hover {
         background: linear-gradient(135deg, #0056b3, #004085);
         color: white;
     }
     
     .edit-comment-btn {
         background: linear-gradient(135deg, #6c757d, #495057);
         border: none;
         color: white;
     }
     
     .edit-comment-btn:hover {
         background: linear-gradient(135deg, #495057, #343a40);
         color: white;
     }
     
     .delete-comment-btn {
         background: linear-gradient(135deg, #dc3545, #c82333);
         border: none;
         color: white;
     }
     
     .delete-comment-btn:hover {
         background: linear-gradient(135deg, #c82333, #a71e2a);
         color: white;
     }
     
     .mark-answer-btn {
         background: linear-gradient(135deg, #ffc107, #e0a800);
         border: none;
         color: #212529;
     }
     
     .mark-answer-btn:hover {
         background: linear-gradient(135deg, #e0a800, #c69500);
         color: #212529;
     }
     
     .flag-comment-btn {
         background: linear-gradient(135deg, #17a2b8, #138496);
         border: none;
         color: white;
     }
     
     .flag-comment-btn:hover {
         background: linear-gradient(135deg, #138496, #117a8b);
         color: white;
     }
     
     .dropdown-toggle {
         background: linear-gradient(135deg, #6c757d, #495057);
         border: none;
         color: white;
     }
     
     .dropdown-toggle:hover {
         background: linear-gradient(135deg, #495057, #343a40);
         color: white;
     }
     
     .dropdown-menu {
         border: none;
         box-shadow: 0 4px 16px rgba(0,0,0,0.15);
         border-radius: 10px;
         padding: 8px 0;
     }
     
     .dropdown-item {
         padding: 8px 16px;
         transition: all 0.2s ease;
     }
     
     .dropdown-item:hover {
         background: linear-gradient(135deg, #f8f9fa, #e9ecef);
         transform: translateX(5px);
     }
     
     .edit-form, .reply-input-section {
         background: linear-gradient(135deg, #f8f9fa, #ffffff);
         border: 1px solid #dee2e6;
         border-radius: 10px;
         padding: 15px;
         margin-top: 15px;
     }
     
     .edit-form textarea, .reply-input-section textarea {
         border: 1px solid #ced4da;
         border-radius: 8px;
         transition: all 0.3s ease;
     }
     
     .edit-form textarea:focus, .reply-input-section textarea:focus {
         border-color: #007bff;
         box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
     }
     
           /* Enhanced Reply Design */
      .replies-to-comment {
          position: relative;
          margin-top: 25px;
          margin-left: 30px;
          padding-left: 25px;
          border-left: 3px solid #007bff;
      }
      
      .replies-to-comment::before {
          content: '';
          position: absolute;
          left: -8px;
          top: -5px;
          width: 12px;
          height: 12px;
          background: #007bff;
          border-radius: 50%;
          border: 3px solid #fff;
          box-shadow: 0 0 0 2px #007bff;
      }
      
      .reply-header {
          color: #007bff;
          font-weight: 600;
          font-size: 0.9rem;
          margin-bottom: 20px;
          display: flex;
          align-items: center;
          gap: 8px;
      }
      
      .reply-header::before {
          content: '↳';
          font-size: 1.2rem;
          color: #007bff;
          font-weight: bold;
      }
      
      .reply-item {
          position: relative;
          margin-bottom: 15px;
          padding-left: 20px;
      }
      
      .reply-item::before {
          content: '';
          position: absolute;
          left: -15px;
          top: 15px;
          width: 2px;
          height: calc(100% - 30px);
          background: linear-gradient(to bottom, #e3f2fd, #bbdefb);
          border-radius: 1px;
      }
      
      .reply-item:last-child::before {
          display: none;
      }
      
      .reply-card {
          background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
          border: 1px solid #e3f2fd;
          border-radius: 12px;
          box-shadow: 0 2px 8px rgba(0,0,0,0.06);
          transition: all 0.3s ease;
      }
      
      .reply-card:hover {
          box-shadow: 0 4px 16px rgba(0,0,0,0.1);
          border-color: #007bff;
          transform: translateY(-1px);
      }
      
      .reply-item .avatar img {
          border: 2px solid #007bff;
          box-shadow: 0 2px 6px rgba(0,123,255,0.2);
      }
      
      .reply-text-section {
          background: linear-gradient(135deg, #ffffff, #f8f9fa);
          border: 1px solid #e3f2fd;
          border-radius: 10px;
          padding: 15px 18px;
          margin-top: 12px;
          position: relative;
      }
      
      .reply-text-section::before {
          content: '';
          position: absolute;
          left: -8px;
          top: 15px;
          width: 0;
          height: 0;
          border-top: 8px solid transparent;
          border-bottom: 8px solid transparent;
          border-right: 8px solid #e3f2fd;
      }
      
      .reply-text {
          color: #495057;
          line-height: 1.7;
          margin: 0;
          font-size: 14px;
      }
      
      .reply-item .comment-actions {
          margin-top: 10px;
      }
      
      .reply-item .comment-actions .btn {
          font-size: 0.75rem;
          padding: 4px 10px;
          border-radius: 15px;
      }
      
      /* Nested reply styling for deeper levels */
      .reply-item .replies-to-comment {
          margin-left: 20px;
          margin-top: 15px;
          padding-left: 20px;
          border-left: 2px solid #bbdefb;
      }
      
      .reply-item .replies-to-comment::before {
          left: -6px;
          width: 8px;
          height: 8px;
          background: #bbdefb;
          border: 2px solid #fff;
          box-shadow: 0 0 0 1px #bbdefb;
      }
     
     .add-comment-section .card {
         background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
         border: 1px solid #dee2e6;
         border-radius: 12px;
     }
     
     .add-comment-section .card-body {
         padding: 20px;
     }
     
     .add-comment-section h6 {
         color: #2c3e50;
         font-weight: 600;
     }
     
     .add-comment-section textarea {
         border: 1px solid #ced4da;
         border-radius: 8px;
         transition: all 0.3s ease;
     }
     
     .add-comment-section textarea:focus {
         border-color: #007bff;
         box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
     }
     
           /* Responsive adjustments */
      @media (max-width: 768px) {
          .comment-actions {
              flex-direction: column;
              align-items: stretch;
              gap: 5px;
          }
          
          .comment-actions .btn {
              width: 100%;
              text-align: center;
          }
          
          .comment-item::before {
              left: -10px;
          }
          
          .replies-to-comment::before {
              left: -15px;
          }
          
          .replies-to-comment {
              margin-left: 15px;
          }
      }
      
      /* Animation for new comments */
      @keyframes slideIn {
          from {
              opacity: 0;
              transform: translateY(-20px);
          }
          to {
              opacity: 1;
              transform: translateY(0);
          }
      }
      
      .comment-item {
          animation: slideIn 0.3s ease-out;
      }
      
      /* Hover effects for interactive elements */
      .comment-card:hover .comment-actions {
          opacity: 1;
      }
      
      .comment-actions {
          opacity: 0.8;
          transition: opacity 0.3s ease;
      }
      
      /* Focus states for accessibility */
      .btn:focus {
          outline: none;
          box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
      }
      
      /* Custom scrollbar for comment sections */
      .comments-container::-webkit-scrollbar {
          width: 6px;
      }
      
      .comments-container::-webkit-scrollbar-track {
          background: #f1f1f1;
          border-radius: 3px;
      }
      
      .comments-container::-webkit-scrollbar-thumb {
          background: #c1c1c1;
          border-radius: 3px;
      }
      
             .comments-container::-webkit-scrollbar-thumb:hover {
           background: #a8a8a8;
       }
       
       /* Additional improvements for comment system */
       .comment-item {
           position: relative;
           margin-bottom: 25px;
       }
       
       .comment-item:last-child {
           margin-bottom: 0;
       }
       
       /* Better spacing for nested comments */
       .comment-item[style*="margin-left"] {
           margin-top: 20px;
       }
       
       /* Enhanced avatar styling */
       .avatar {
           position: relative;
       }
       
       .avatar::after {
           content: '';
           position: absolute;
           bottom: -2px;
           right: -2px;
           width: 12px;
           height: 12px;
           background: #28a745;
           border: 2px solid #fff;
           border-radius: 50%;
           box-shadow: 0 1px 3px rgba(0,0,0,0.2);
       }
       
       /* Improved badge styling */
       .badge.bg-light-purple {
           background: linear-gradient(135deg, #e6e6fa, #d8d8f8) !important;
           color: #6a5acd !important;
           border: 1px solid #c8c8f0;
           font-weight: 600;
           letter-spacing: 0.5px;
       }
       
       /* Better button spacing in comment actions */
       .comment-actions .btn:not(:last-child) {
           margin-right: 8px;
       }
       
       /* Enhanced hover effects */
       .comment-card:hover .avatar img,
       .reply-card:hover .avatar img {
           transform: scale(1.05);
           transition: transform 0.3s ease;
       }
       
       /* Smooth transitions for all interactive elements */
       .comment-card *,
       .reply-card * {
           transition: all 0.2s ease;
       }
       
       /* Enhanced reply separation and styling */
       .replies-to-comment {
           margin-top: 30px;
           padding-top: 20px;
           border-top: 1px solid #e9ecef;
       }
       
       .reply-item {
           margin-bottom: 20px;
           position: relative;
       }
       
       .reply-item:last-child {
           margin-bottom: 0;
       }
       
       .reply-item .reply-card {
           border-left: 4px solid #007bff;
           background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
       }
       
       .reply-item .avatar img,
       .reply-item .avatar div {
           border: 2px solid #007bff;
           box-shadow: 0 2px 6px rgba(0,123,255,0.2);
       }
       
       .reply-item .comment-actions .btn {
           font-size: 0.75rem;
           padding: 4px 10px;
           border-radius: 15px;
       }
       
       .reply-item .comment-actions .btn:not(:last-child) {
           margin-right: 6px;
       }
       
               /* Better visual separation between main comments and replies */
        .main-comment-item {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 30px;
            margin-bottom: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .main-comment-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        
        /* Separate replies section styling */
        .replies-section {
            margin-left: 40px;
            padding-left: 20px;
            border-left: 3px solid #007bff;
            position: relative;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .replies-section::before {
            content: '';
            position: absolute;
            left: -8px;
            top: -5px;
            width: 12px;
            height: 12px;
            background: #007bff;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #007bff;
        }
        
        .replies-section .reply-header {
            color: #007bff;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e3f2fd;
        }
        
        .replies-section .reply-header::before {
            content: '↳';
            font-size: 1.2rem;
            color: #007bff;
            font-weight: bold;
        }
        
        /* Enhanced reply item styling */
        .reply-item {
            background: #fff;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e3f2fd;
        }
        
        .reply-item:last-child {
            margin-bottom: 0;
        }
        
        .reply-card {
            border: none;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        
        .reply-text-section {
            background: #f8f9fa;
            border: 1px solid #e3f2fd;
            border-radius: 6px;
            padding: 12px 15px;
        }
        /* Comment System Styles - Dual Structure */
        .comments-section {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-top: 30px;
        }

        .comments-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .comments-header h5 {
            color: #2c3e50;
            font-weight: 600;
        }

        /* Main Comment Styles */
        .main-comment {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
            padding: 20px;
        }

        .main-comment:last-child {
            margin-bottom: 0;
        }

        .comment-card {
            border: none;
            box-shadow: none;
        }

        /* User Avatar Styles */
        .user-avatar {
            flex-shrink: 0;
        }

        .avatar-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #6f42c1;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        /* User Info Styles */
        .user-details {
            flex-grow: 1;
        }

        .user-name {
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
            font-size: 16px;
        }

        .user-meta {
            align-items: center;
        }

        .badge.bg-light-purple {
            background-color: #e9d5ff !important;
            color: #6b21a8 !important;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .comment-time {
            color: #6c757d;
            font-size: 0.85rem;
        }

        /* Reply Indicator for Flat View */
        .reply-indicator {
            color: #007bff;
            font-weight: 600;
            font-size: 0.85rem;
            background: #e3f2fd;
            padding: 2px 8px;
            border-radius: 12px;
            border: 1px solid #bbdefb;
        }

        /* Comment Text Styles */
        .comment-text {
            color: #495057;
            line-height: 1.6;
            margin: 0;
        }

        .comment-text p {
            margin: 0;
            font-size: 14px;
        }

        /* Reply Input Section */
        .reply-input-section {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .reply-input-section textarea {
            border: 1px solid #e3f2fd;
            border-radius: 8px;
            padding: 10px 15px;
            resize: vertical;
        }

        .reply-input-section textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        /* Action Buttons Styles */
        .comment-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
            align-items: center;
        }

        .comment-actions .btn {
            border-radius: 6px;
            font-size: 0.85rem;
            padding: 6px 12px;
        }

        /* Hierarchical Comments Section */
        .hierarchical-comments {
            border-top: 2px solid #e9ecef;
            padding-top: 25px;
        }

        .hierarchical-comments h6 {
            color: #6c757d;
            font-weight: 600;
        }

        /* Main Comment Thread Styles */
        .main-comment-thread {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
            padding: 20px;
        }

        /* Replies Section */
        .replies-section {
            margin-top: 20px;
            margin-left: 40px;
            position: relative;
        }

        .replies-section::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #007bff;
        }

        /* Reply Comment Styles */
        .reply-comment {
            margin-bottom: 20px;
            position: relative;
        }

        .reply-comment::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 20px;
            width: 15px;
            height: 2px;
            background: #007bff;
        }

        .reply-comment::after {
            content: '';
            position: absolute;
            left: -20px;
            top: 15px;
            width: 8px;
            height: 8px;
            background: #007bff;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #007bff;
        }

        .reply-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e3f2fd;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }

        .reply-header {
            margin-bottom: 15px;
        }

        .reply-text {
            color: #495057;
            line-height: 1.6;
        }

        .reply-text p {
            margin: 0;
            font-size: 14px;
        }

        .reply-link {
            color: #007bff;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .reply-link:hover {
            text-decoration: underline;
        }

        /* Edit Form Styles */
        .edit-form {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .edit-form textarea {
            border: 1px solid #e3f2fd;
            border-radius: 8px;
            padding: 10px 15px;
            resize: vertical;
        }

        .edit-form textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        /* No Comments Style */
        .text-center.text-muted.py-5 {
            color: #6c757d;
        }

        .fa-light.fa-comments {
            color: #dee2e6;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .replies-section {
                margin-left: 20px;
            }
            
            .comment-actions {
                flex-direction: column;
                gap: 5px;
            }
            
            .comment-actions .btn {
                width: 100%;
            }
        }
</style>
 @endsection
@section('content')
<!-- Content -->
<div class="container-fluid">
    <section class="cds-ty-dashboard-breadcrumb-container">
        <div class="cds-main-layout-header">
            <div class="breadcrumb-conatiner">
                <ol class="breadcrumb">
                    <i class="fa-grid-2 fa-regular"></i>
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('associates') }}">Associates</a></li>
                    <li class="active breadcrumb-item" aria-current="page">{{$pageTitle}}</li>
                </ol>
            </div>
            <div class="cds-heading">
                <div class="cds-heading-icon">
                    <i class="fa-light fa-file-contract"></i>
                </div>
                <h1>{{$pageTitle}}</h1>
            </div>
        </div>
    </section>

    <!-- Agreement Details Section -->
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-body">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Agreement Details</h5>
                            @if($agreement->is_support_accept == 0)
                            <div class="alert alert-danger">
                                <span>your agreement is under review by support team</span>
                            </div>
                            @endif
                            <div class="card-toolbar">
                                @if($agreement->pdf)
                                    <a href="{{baseUrl('agreement/download-pdf/'.$agreement->unique_id)}}" download class="btn btn-primary btn-sm"  id="downloadPdfBtn" onclick="handlePdfDownload(this)">
                                        <i class="fa-light fa-download"></i> Download PDF
                                    </a>
                                @endif
                                <a href="{{ baseUrl('associates') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-light fa-arrow-left"></i> Back to Associates
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Agreement Information Table -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Agreement Name</th>
                                                    <td>{{ $agreement->template_name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Agreement ID</th>
                                                    <td>{{ $agreement->unique_id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Professional</th>
                                                    <td>
                                                        @php
                                                            $professional = App\Models\User::find($agreement->professional_id);
                                                        @endphp
                                                        {{ $professional ? $professional->first_name . ' ' . $professional->last_name : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Associate</th>
                                                    <td>
                                                        @php
                                                            $associate = App\Models\User::find($agreement->associate_id);
                                                        @endphp
                                                        {{ $associate ? $associate->first_name . ' ' . $associate->last_name : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Platform Fees</th>
                                                    <td>${{ number_format($agreement->platform_fees, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sharing Fees</th>
                                                    <td>{{ $agreement->sharing_fees }}%</td>
                                                </tr>
                                                <tr>
                                                    <th>Created Date</th>
                                                    <td>{{ $agreement->created_at ? $agreement->created_at->format('F d, Y') : 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>PDF File</th>
                                                    <td>
                                                        @if($agreement->pdf)
                                                            <span class="badge bg-success">Available</span>
                                                            <a href="/storage/agreements/{{ $agreement->pdf }}" target="_blank" class="ms-2">
                                                                <i class="fa-light fa-eye"></i> View PDF
                                                            </a>
                                                        @else
                                                            <span class="badge bg-warning">Not Generated</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Agreement Content Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Agreement Content</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="agreement-content-wrapper" style="background: #fff; padding: 20px; border: 1px solid #dee2e6; border-radius: 5px;">
                                                {!! $agreement->agreement !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                          
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!-- Comments Section -->
     <div class="cds-ty-dashboard-box">
         <div class="cds-ty-dashboard-box-body">
             <div class="row">
                 <div class="col-xl-12">
                     <div class="card">
                         <div class="card-header">
                             <h5 class="card-title">Comments & Discussion</h5>
                         </div>
                         <div class="card-body">
                             <!-- Add Comment Form -->
                             <div class="add-comment-section mb-4">
                                 <div class="card">
                                     <div class="card-body">
                                         <h6 class="card-title mb-3">
                                             <i class="fa-light fa-comment-plus"></i> Add a Comment
                                         </h6>
                                                                                         <form id="addCommentForm" method="POST" action="javascript:void(0);">
                                             <div class="form-group">
                                                 <textarea class="form-control" id="commentText" name="comment" rows="3" placeholder="Write your comment here..." required></textarea>
                                             </div>
                                             <input type="hidden" id="agreementId" value="{{ $agreement->id }}">
                                             <input type="hidden" id="parentCommentId" value="">
                                             <div class="mt-3">
                                                                                                   <button type="submit" class="btn btn-primary">
                                                      <i class="fa-light fa-comment"></i> Post Comment
                                                  </button>
                                                 <button type="button" id="cancelReply" class="btn btn-secondary ms-2" style="display: none;">
                                                     <i class="fa-light fa-times"></i> Cancel Reply
                                                 </button>
                                             </div>
                                         </form>
                                     </div>
                                 </div>
                             </div>

                             <!-- Comments List -->
                             <div id="commentsContainer">
                                 <div class="text-center py-4">
                                     <div class="spinner-border text-primary" role="status">
                                         <span class="visually-hidden">Loading comments...</span>
                                     </div>
                                     <p class="mt-2">Loading comments...</p>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
 <!-- End Content -->
 @endsection

@section('styles')
<style>
    .agreement-content-wrapper {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        color: #333;
    }
    
    .agreement-content-wrapper h1,
    .agreement-content-wrapper h2,
    .agreement-content-wrapper h3,
    .agreement-content-wrapper h4,
    .agreement-content-wrapper h5,
    .agreement-content-wrapper h6 {
        color: #2c3e50;
        margin-top: 20px;
        margin-bottom: 10px;
    }
    
    .agreement-content-wrapper p {
        margin-bottom: 15px;
        text-align: justify;
    }
    
    .agreement-content-wrapper table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    
    .agreement-content-wrapper table td,
    .agreement-content-wrapper table th {
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    .agreement-content-wrapper table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .pdf-preview-container {
        background: #f8f9fa;
        padding: 10px;
    }
    
    .card-toolbar {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
         .table th {
         background-color: #f8f9fa;
         font-weight: 600;
     }

     .comment-item {
         border-left: 3px solid #e9ecef;
     }

     .comment-item .card {
         border: 1px solid #dee2e6;
         box-shadow: 0 1px 3px rgba(0,0,0,0.1);
     }

     .comment-actions {
         opacity: 0.7;
         transition: opacity 0.3s;
     }

     .comment-item:hover .comment-actions {
         opacity: 1;
     }

     .replies-section {
         border-left: 2px solid #007bff;
         padding-left: 15px;
     }

     .avatar img {
         object-fit: cover;
     }

     .comment-card {
         transition: all 0.3s ease;
         border: 1px solid #e9ecef;
     }

     .comment-card:hover {
         box-shadow: 0 4px 8px rgba(0,0,0,0.1);
         transform: translateY(-2px);
     }

     .comment-text {
         line-height: 1.6;
         color: #495057;
     }
     
     .comment-text-section {
         background-color: #fff;
         border: 1px solid #e9ecef;
         border-radius: 8px;
         padding: 12px 16px;
         box-shadow: 0 1px 3px rgba(0,0,0,0.1);
         margin-top: 8px;
         transition: all 0.3s ease;
     }
     
     .comment-text-section:hover {
         box-shadow: 0 2px 6px rgba(0,0,0,0.15);
         border-color: #007bff;
     }
     
     .comment-text-section .comment-text {
         color: #495057;
         line-height: 1.6;
         margin: 0;
         font-size: 14px;
     }
     


     .add-comment-section .card {
         background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
         border: 1px solid #dee2e6;
     }

     .comment-actions .btn {
         transition: all 0.2s ease;
     }

     .comment-actions .btn:hover {
         transform: scale(1.05);
     }

     .replies-section {
         position: relative;
         margin-top: 15px;
     }

     .replies-section::before {
         content: '';
         position: absolute;
         left: -15px;
         top: 0;
         bottom: 0;
         width: 2px;
         background: linear-gradient(to bottom, #007bff, #6c757d);
     }
     
     .reply-item {
         margin-left: 20px;
         border-left: 3px solid #007bff;
         padding-left: 15px;
         margin-bottom: 10px;
     }
     
     .reply-item .card {
         background-color: #fff;
         border: 1px solid #e9ecef;
         box-shadow: 0 1px 3px rgba(0,0,0,0.1);
     }
     
     .reply-item .comment-text-section {
         background-color: #f8f9fa;
         border-color: #007bff;
     }
     
     .replies-section h6 {
         font-size: 0.875rem;
         color: #6c757d;
         border-bottom: 1px solid #e9ecef;
         padding-bottom: 8px;
         margin-bottom: 15px;
     }
     
     .reply-item .text-muted.small {
         font-size: 0.75rem;
         color: #6c757d;
         font-style: italic;
     }
     
     /* Comment with replies styling */
     .comment-with-replies {
         border: 1px solid #e9ecef;
         border-radius: 8px;
         padding: 15px;
         background-color: #fff;
     }
     
     .replies-to-comment {
         border-left: 3px solid #17a2b8;
         padding-left: 15px;
         margin-top: 15px;
     }
     
     .reply-header {
         color: #17a2b8;
         font-weight: 600;
         border-bottom: 1px solid #e9ecef;
         padding-bottom: 8px;
     }
     
     .reply-card {
         border-left: 4px solid #17a2b8;
         background-color: #f8f9fa;
     }
     
     .reply-context .badge {
         font-size: 0.75rem;
         padding: 5px 10px;
     }
     
     .reply-text-section {
         background-color: #fff;
         border: 1px solid #e9ecef;
         border-radius: 6px;
         padding: 10px 12px;
         margin-top: 8px;
     }
      
      .reply-item .avatar img {
          width: 30px !important;
          height: 30px !important;
      }
      
      .reply-item .comment-actions .btn {
          padding: 0.25rem 0.5rem;
          font-size: 0.75rem;
      }
 </style>
 @endsection

 @section('javascript')
 <script>
 $(document).ready(function() {
           // Load Comments on Page Load
           loadComments();
           
           // Add Comment
             $('#addCommentForm').on('submit', function(e) {
           e.preventDefault();
           
           const commentText = $('#commentText').val().trim();
           const agreementId = $('#agreementId').val();
           const parentId = $('#parentCommentId').val();
           
           console.log('Form submitted - Comment Text:', commentText);
           console.log('Form submitted - Agreement ID:', agreementId);
           console.log('Form submitted - Parent ID:', parentId);
           
           if (!commentText) {
               alert('Please enter a comment');
               return;
           }
          
          // Disable submit button
          const submitBtn = $(this).find('button[type="submit"]');
          submitBtn.prop('disabled', true).html('<i class="fa-light fa-spinner fa-spin"></i> Posting...');
          
          // Create form data
          const formData = new FormData();
          formData.append('agreement_id', agreementId);
          formData.append('comment', commentText);
          formData.append('parent_id', parentId || '');
          formData.append('_token', '{{ csrf_token() }}');
          
          // Send request
          fetch('{{ baseUrl("agreement-comments/store") }}', {
              method: 'POST',
              body: formData,
              headers: {
                  'X-Requested-With': 'XMLHttpRequest'
              }
          })
                     .then(response => {
               console.log('Raw response:', response);
               return response.json();
           })
           .then(data => {
               console.log('Parsed data:', data);
               if (data.success) {
                   // Clear form
                   $('#commentText').val('');
                   $('#parentCommentId').val('');
                   $('#cancelReply').hide();
                   
                   // Show success message
                   successMessage('Comment added successfully!', 'success');
                   
                   // Reload comments to show the new comment
                   loadComments();
               } else {
                   errorMessage(data.message || 'Failed to add comment', 'error');
               }
           })
                     .catch(error => {
               console.error('Error:', error);
               console.error('Error details:', error.message);
               errorMessage('Error adding comment: ' + error.message, 'error');
           })
          .finally(() => {
              // Re-enable button
              submitBtn.prop('disabled', false).html('<i class="fa-light fa-comment"></i> Post Comment');
          });
      });

           // Reply to Comment
      $(document).on('click', '.reply-comment-btn', function() {
          const commentId = $(this).data('comment-id');
          const commentText = $(this).closest('.comment-item').find('.comment-text-section p').text();
          
          console.log('Reply button clicked for comment ID:', commentId);
          console.log('Comment text:', commentText);
          
          $('#parentCommentId').val(commentId);
          $('#commentText').val('').focus();
          $('#cancelReply').show();
          
          console.log('Parent comment ID set to:', $('#parentCommentId').val());
          
          // Scroll to comment form
          $('#addCommentForm').get(0).scrollIntoView({ behavior: 'smooth' });
      });

     // Cancel Reply
     $('#cancelReply').on('click', function() {
         $('#parentCommentId').val('');
         $('#cancelReply').hide();
     });

     // Edit Comment
     $(document).on('click', '.edit-comment-btn', function() {
         const commentId = $(this).data('comment-id');
         const editForm = $(`#editForm${commentId}`);
         const commentContent = $(this).closest('.comment-item').find('.comment-text-section');
         
         commentContent.hide();
         editForm.show();
     });

     // Cancel Edit
     $(document).on('click', '.cancel-edit-btn', function() {
         const commentId = $(this).data('comment-id');
         const editForm = $(`#editForm${commentId}`);
         const commentContent = editForm.closest('.comment-item').find('.comment-text-section');
         
         editForm.hide();
         commentContent.show();
     });

                       // Update Comment
       $(document).on('submit', '.editCommentForm', function(e) {
           e.preventDefault();
           
           const form = $(this);
           const commentId = form.data('comment-id');
           const commentText = form.find('textarea[name="comment"]').val().trim();
           
           if (!commentText) {
               alert('Please enter a comment');
               return;
           }
           
           const formData = new FormData();
           formData.append('comment', commentText);
           formData.append('_token', '{{ csrf_token() }}');
           
           fetch('{{ baseUrl("agreement-comments/update") }}/' + commentId, {
               method: 'POST',
               body: formData,
               headers: {
                   'X-HTTP-Method-Override': 'PUT',
                   'X-Requested-With': 'XMLHttpRequest'
               }
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   // Show success message
                   successMessage('Comment updated successfully!', 'success');
                   
                   // Reload comments to show the updated comment
                   loadComments();
               } else {
                   errorMessage(data.message || 'Failed to update comment', 'error');
               }
           })
           .catch(error => {
               console.error('Error:', error);
               errorMessage('Error updating comment. Please try again.', 'error');
           });
       });

                       // Delete Comment
       $(document).on('click', '.delete-comment-btn', function() {
           if (!confirm('Are you sure you want to delete this comment?')) {
               return;
           }
           
           const commentId = $(this).data('comment-id');
           const commentItem = $(this).closest('.comment-item');
           
           const formData = new FormData();
           formData.append('_token', '{{ csrf_token() }}');
           
           fetch('{{ baseUrl("agreement-comments/delete") }}/' + commentId, {
               method: 'POST',
               body: formData,
               headers: {
                   'X-HTTP-Method-Override': 'DELETE',
                   'X-Requested-With': 'XMLHttpRequest'
               }
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   // Show success message
                   successMessage('Comment deleted successfully!', 'success');
                   
                   // Reload comments to show the updated list
                   loadComments();
               } else {
                   errorMessage(data.message || 'Failed to delete comment', 'error');
               }
           })
           .catch(error => {
               console.error('Error:', error);
               errorMessage('Error deleting comment. Please try again.', 'error');
           });
       });

           // Load Comments function removed - using direct page reload instead

                        
       
       // Load Comments via AJAX
       function loadComments() {
           const agreementId = $('#agreementId').val();
           
           // Show loading spinner
           $('#commentsContainer').html(`
               <div class="text-center py-4">
                   <div class="spinner-border text-primary" role="status">
                       <span class="visually-hidden">Loading comments...</span>
                   </div>
                   <p class="mt-2">Loading comments...</p>
               </div>
           `);
           
           // Fetch comments from server
           fetch(`{{ baseUrl('agreement-comments/view') }}/${agreementId}`)
               .then(response => {
                   if (!response.ok) {
                       throw new Error('Network response was not ok');
                   }
                   return response.text();
               })
               .then(html => {
                   // Replace the comments container with the rendered HTML
                   $('#commentsContainer').html(html);
                   console.log('Comments loaded successfully');
               })
               .catch(error => {
                   console.error('Error loading comments:', error);
                   $('#commentsContainer').html(`
                       <div class="text-center text-muted py-4">
                           <i class="fa-light fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                           <p>Failed to load comments. Please refresh the page.</p>
                           <button class="btn btn-primary btn-sm" onclick="loadComments()">Retry</button>
                       </div>
                   `);
               });
       }
       

           // Safely get user data with fallbacks
           const userName = comment.user ? `${comment.user.first_name || ''} ${comment.user.last_name || ''}`.trim() : 'Unknown User';
           const userAvatar = comment.user && comment.user.avatar ? comment.user.avatar : '/assets/images/default-avatar.png';
           const userInitials = comment.user ? `${comment.user.first_name ? comment.user.first_name.charAt(0).toUpperCase() : ''}${comment.user.last_name ? comment.user.last_name.charAt(0).toUpperCase() : ''}` : 'U';
           
           // Get the appropriate template based on type
           let template = '';
           
           if (type === 'main') {
               template = `
                   <div class="comment-item mb-3" data-comment-id="${comment.id}">
                       <div class="card comment-card">
                           <div class="card-body">
                               <!-- User Info Section -->
                               <div class="d-flex justify-content-between align-items-start mb-3">
                                   <div class="d-flex align-items-center">
                                       <div class="avatar me-3">
                                           <img src="${userAvatar}" alt="${userName}" class="rounded-circle" width="40" height="40">
                                       </div>
                                       <div>
                                           <h6 class="mb-0 fw-bold">${userName}</h6>
                                           <small class="text-muted">
                                               <i class="fa-light fa-clock"></i> Just now
                                           </small>
                                       </div>
                                   </div>
                                   <div class="comment-actions">
                                       <button class="btn btn-sm btn-outline-primary edit-comment-btn" data-comment-id="${comment.id}" title="Edit Comment">
                                           <i class="fa-light fa-edit"></i>
                                       </button>
                                       <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="${comment.id}" title="Delete Comment">
                                           <i class="fa-light fa-trash"></i>
                                       </button>
                                       <button class="btn btn-sm btn-outline-secondary reply-comment-btn" data-comment-id="${comment.id}" title="Reply to Comment">
                                           <i class="fa-light fa-reply"></i> Reply
                                       </button>
                                   </div>
                               </div>
                               
                               <!-- Comment Text Section -->
                               <div class="comment-text-section mt-2">
                                   <p class="comment-text mb-0">${comment.comment}</p>
                               </div>

                               <!-- Edit Comment Form (Hidden by default) -->
                               <div class="edit-form mt-2" id="editForm${comment.id}" style="display: none;">
                                   <form class="editCommentForm" data-comment-id="${comment.id}">
                                       <div class="form-group">
                                           <textarea class="form-control" name="comment" rows="2" required>${comment.comment}</textarea>
                                       </div>
                                       <div class="mt-2">
                                           <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                           <button type="button" class="btn btn-sm btn-secondary cancel-edit-btn" data-comment-id="${comment.id}">Cancel</button>
                                       </div>
                                   </form>
                               </div>

                               <!-- Replies section -->
                               <div class="replies-section mt-3" id="replies${comment.id}">
                                   <!-- Replies will be added here -->
                               </div>
                           </div>
                       </div>
                   </div>
               `;
           } else if (type === 'reply') {
               template = `
                   <div class="reply-item mb-2" data-reply-id="${comment.id}">
                       <div class="card comment-card" style="border-left: 3px solid #007bff;">
                           <div class="card-body py-2">
                               <div class="d-flex align-items-start">
                                   <div class="avatar me-2">
                                       <img src="${userAvatar}" alt="${userName}" class="rounded-circle" width="30" height="30">
                                   </div>
                                   <div class="flex-grow-1">
                                       <div class="d-flex justify-content-between align-items-start">
                                           <div>
                                               <strong class="text-primary">${userName}</strong>
                                               <small class="text-muted ms-2">
                                                   <i class="fa-light fa-clock"></i> Just now
                                               </small>
                                           </div>
                                           <div class="comment-actions">
                                               <button class="btn btn-sm btn-outline-primary edit-comment-btn" data-comment-id="${comment.id}">
                                                   <i class="fa-light fa-edit"></i>
                                               </button>
                                               <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="${comment.id}">
                                                   <i class="fa-light fa-trash"></i>
                                               </button>
                                           </div>
                                       </div>
                                       <div class="comment-text-section mt-1">
                                           <p class="comment-text mb-0">${comment.comment}</p>
                                       </div>
                                       <div class="edit-form mt-2" id="editForm${comment.id}" style="display: none;">
                                           <form class="editCommentForm" data-comment-id="${comment.id}">
                                               <div class="form-group">
                                                   <textarea class="form-control" name="comment" rows="2" required>${comment.comment}</textarea>
                                               </div>
                                               <div class="mt-2">
                                                   <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                   <button type="button" class="btn btn-sm btn-secondary cancel-edit-btn" data-comment-id="${comment.id}">Cancel</button>
                                               </div>
                                           </form>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               `;
           }
           


      // Show Alert
      
 });

function handlePdfDownload(button) {
     const originalText = button.innerHTML;
     const originalClass = button.className;
     
     // Show loading state
     button.innerHTML = '<i class="fa-light fa-spinner fa-spin"></i> Generating PDF...';
     button.className = button.className.replace('btn-primary', 'btn-secondary');
     button.disabled = true;
     
     // Reset button after a short delay
     setTimeout(() => {
         button.innerHTML = originalText;
         button.className = originalClass;
         button.disabled = false;
     }, 3000);
 }
 </script>
 @endsection