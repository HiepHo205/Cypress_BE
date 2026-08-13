export interface NewsImage {
    url?: string;
    public_id?: string;
}

export interface NewsItem {
    id?: string | null;
    title?: string;
    description?: string;
    category?: string;
    date?: string;
    featured?: boolean;
    image?: NewsImage | string | null;
}

export interface NewsForm {
    id: string | null;
    title: string;
    description: string;
    date: string;
    category: string;
    imageFile: File | null;
    imagePreview: string;
    featured: boolean;
    isNew: boolean;
}

export interface NewsItemInput {
    id?: string | null;
    title: string;
    description?: string;
    date?: string;
    category?: string;
    status?: string;
    featured?: boolean;
}
